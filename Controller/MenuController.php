<?php

namespace ASK\MenuBundle\Controller;

use ASK\MenuBundle\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Menu controller.
 *
 */
class MenuController extends Controller
{
    
    const MESSAGE_MENU_SAVED = 'Menu saved';
    
    /**
     * Lists all menu entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $subMenu = $request->get('sub-menu', null);

        $node = null;
        $menuRepository = $em->getRepository('MenuBundle:Menu');

        if (!empty($subMenu)) {
            $subMenu = $menuRepository->find($subMenu);
        }

        $menuQueryBuilder = $em->getRepository('MenuBundle:Menu')->getNodesHierarchyQueryBuilder($subMenu, true);
        //$alias = current($menuQueryBuilder->getDQLPart('from'))->getAlias();
        $menus = $menuQueryBuilder->getQuery()->getResult();
        return $this->render('@Menu/menu/index.html.twig', array(
            'menus' => $menus,
            'subMenu' => (!empty($subMenu) ? $subMenu->getId() : null)

        ));
    }

    /**
     * Creates a new menu entity.
     *
     */
    public function newAction(Request $request)
    {
        $menu = new Menu();
        $subMenu = $request->get('sub-menu');

        $formParams = [];

        if (!empty($subMenu)) {
            $formParams = ['subMenu' => $subMenu];
        }

        $form = $this->createForm('ASK\MenuBundle\Form\MenuType', $menu);
        $parentMenu = null;
        
        $form->handleRequest($request);
        if(!empty($subMenu)) {
            $subMenuEntity = $this->getDoctrine()->getRepository(Menu::class)->find($subMenu);
            if (!empty($subMenuEntity)) {
                $menu->setParent($subMenuEntity);
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            if (!empty($formData->getParent())) {
                $menu->setParent($formData->getParent());
            }

            $em = $this->getDoctrine()->getManager();

            $em->persist($menu);
            $em->flush();

            $flashMessage = self::MESSAGE_MENU_SAVED.' '.$menu->getTitle();

            $this->addFlash('menuSaved', $flashMessage);

            return $this->redirectToRoute('admin_menu_show', array('id' => $menu->getId()));
        }

        
        return $this->render('@Menu/menu/new.html.twig', array(
            'menu' => $menu,
            'form' => $form->createView(),
            'subMenu' => $subMenu
        ));
    }

    /**
     * Finds and displays a menu entity.
     *
     */
    public function showAction(Menu $menu)
    {
        $deleteForm = $this->createDeleteForm($menu);
        
        $parent = $menu->getParent();
        $subMenu = (!empty($parent) ? $parent->getId() : null);

        return $this->render('@Menu/menu/show.html.twig', array(
            'menu' => $menu,
            'subMenu' => $subMenu,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing menu entity.
     *
     */
    public function editAction(Request $request, Menu $menu)
    {
        $deleteForm = $this->createDeleteForm($menu);
        $editForm = $this->createForm('ASK\MenuBundle\Form\MenuType', $menu);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_menu_edit', array('id' => $menu->getId()));
        }

        return $this->render('@Menu/menu/edit.html.twig', array(
            'menu' => $menu,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing menu entity.
     *
     */
    public function upAction(Request $request, Menu $menu)
    {

        if (!$menu) {
            throw new NotFoundHttpException('Page not found');
        }

        if (empty($menu->getParent())) {
            throw new NotFoundHttpException('You can not move up this node');
        }


        $em = $this->getRepository();

        $em->moveUp($menu);

        return $this->redirectToRoute('admin_menu_index', ['sub-menu'=>$menu->getParent()->getId()]);        
    }

    /**
     * Displays a form to edit an existing menu entity.
     *
     */
    public function downAction(Request $request, Menu $menu)
    {

        if (!$menu) {
            throw new NotFoundHttpException('Page not found');
        }

        if (empty($menu->getParent())) {
            throw new NotFoundHttpException('You can not move down this node');
        }


        $em = $this->getRepository();

        $em->moveDown($menu);

        return $this->redirectToRoute('admin_menu_index', ['sub-menu'=>$menu->getParent()->getId()]);

    }

    /**
     * Deletes a menu entity.
     *
     */
    public function deleteAction(Request $request, Menu $menu)
    {
        $form = $this->createDeleteForm($menu);
        $form->handleRequest($request);

        $parent = $menu->getParent();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($menu);
            $em->flush();
        }

        $params = [];

        if (!empty($parent)) {
            $params = [
                'sub-menu' => $parent->getId()
            ];
        }

        return $this->redirectToRoute('admin_menu_index', $params);
    }

    /**
     * Creates a form to delete a menu entity.
     *
     * @param Menu $menu The menu entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Menu $menu)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_menu_delete', array('id' => $menu->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository(Menu::class);
    }
}
