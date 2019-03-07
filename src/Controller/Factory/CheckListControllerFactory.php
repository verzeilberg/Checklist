<?php
namespace CheckList\Controller\Factory;

use Interop\Container\ContainerInterface;
use CheckList\Controller\CheckListController;
use Zend\ServiceManager\Factory\FactoryInterface;
use CheckList\Service\checkListService;
use CheckList\Service\checkListItemService;
use CheckList\Service\checkListFieldService;
use CheckList\Service\checkListAnswerService;
use CheckList\Service\givenAnswerService;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class CheckListControllerFactory implements FactoryInterface
{
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $viewhelpermanager = $container->get('ViewHelperManager');
        $checkListService = new checkListService($entityManager);
        $checkListItemService = new checkListItemService($entityManager);
        $checkListFieldService = new checkListFieldService($entityManager);
        $checkListAnswerService = new checkListAnswerService($entityManager);
        $givenAnswerService = new givenAnswerService($entityManager, $checkListFieldService);
        
        return new CheckListController(
            $entityManager,
            $viewhelpermanager,
            $checkListService,
            $checkListItemService,
            $checkListFieldService,
            $checkListAnswerService,
            $givenAnswerService
        );
        
    }
}