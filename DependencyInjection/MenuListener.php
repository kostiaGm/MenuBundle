<?php
/**
 * Created by PhpStorm.
 * User: kot
 * Date: 02.02.19
 * Time: 16:50
 */

namespace ASK\MenuBundle\DependencyInjection;
use Gedmo\References\ReferencesListener;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuListener extends ReferencesListener
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;
    /**
     * @var array
     */
    protected $managers
        = [
            'document' => 'doctrine.odm.mongodb.document_manager',
            'entity'   => 'doctrine.orm.default_entity_manager'
        ];

    /**
     * @param ContainerInterface $container
     * @param array              $managers
     */
    public function __construct(ContainerInterface $container, array $managers = array())
    {
        $this->container = $container;
        $managers = array_merge($managers, $this->managers);

        parent::__construct($managers);
    }

    /**
     * @param $type
     *
     * @return object
     */
    public function getManager($type)
    {
        return $this->container->get($this->managers[$type]);
    }
}