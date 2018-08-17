<?php
namespace CheckList\Controller\Factory;

use Interop\Container\ContainerInterface;
use CheckList\Controller\CheckListAjaxController;
use Zend\ServiceManager\Factory\FactoryInterface;
use CheckList\Service\checkListService;
use CheckList\Service\checkListItemService;
/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class CheckListAjaxControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $checkListService = new checkListService($entityManager);
        $checkListItemService = new checkListItemService($entityManager);
        return new CheckListAjaxController($entityManager, $checkListService, $checkListItemService);
    }
}