<?php

namespace ASK\MenuBundle\Controller;

use ASK\MenuBundle\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $repo = $this->getDoctrine()->getRepository(Menu::class);
        $counter = 0;
        $options = array(
            'decorate' => true,
            'rootOpen' =>  function ($tree) {
                if(count($tree) && ($tree[0]['lvl'] == 0)){
                    return '<ul class="navbar-nav mr-auto">'."\n";
                }
                return '<div class="dropdown-menu" aria-labelledby="navbarDropdown">'."\n";
            } ,
            'rootClose' => function ($tree) {
                if(count($tree) && ($tree[0]['lvl'] == 0)){
                    return '</ul>'."\n";
                }
                return '</div>'."\n";
            },
            'childOpen' =>  function ($tree) {
                if(count($tree) && ($tree['lvl'] == 0)){
                    return '<li class="nav-item dropdown">'."\n";
                }
                return '';
            },
            'childClose' =>  function ($tree) {
                if(count($tree) && ($tree['lvl'] == 0)){
                    return '</li>'."\n";
                }
                return '';
            },
            'nodeDecorator' => function($node) use (&$counter) {

                $count = count($node['__children']);
                $ret = '';
                if ($count > 0) {
                    if ($counter == 0) {
                        $ret = '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.$node["title"].'</a>'."\n";
                    } else {
                        $ret = ' <a class="dropdown-item" href="#">'.$node["title"].'</a>'."\n";
                    }

                    if ($count == ($counter - 1)) {
                        $ret .= '</div>'."\n";
                    }

                } else {
                    return ' <a class="nav-link" href="#">'.$node["title"].'</a>'."\n" ;
                }

                $counter++;
                return $ret;
            }
        );


        $query = $this->getDoctrine()->getManager()
            ->createQueryBuilder()
            ->select('node')
            ->from(Menu::class, 'node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->where('node.status = 1')
            ->getQuery()
        ;

        $menu = $repo->buildTree($query->getArrayResult(), $options);

//        $menu = $repo->childrenHierarchy(
//            null, /* starting from root nodes */
//            false, /* true: load all children, false: only direct */
//            $options
//        );



        return $this->render('@Menu/Default/index.html.twig', [
            'menu' => $menu
        ]);
    }
}
