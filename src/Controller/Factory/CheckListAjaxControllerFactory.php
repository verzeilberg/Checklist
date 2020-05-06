<?php
namespace CheckList\Controller\Factory;

use CheckList\Service\checkListFieldService;
use CheckList\Service\givenAnswerService;
use Interop\Container\ContainerInterface;
use CheckList\Controller\CheckListAjaxController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use CheckList\Service\checkListService;
use CheckList\Service\checkListItemService;
use CheckList\Service\checkListAnswerService;

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
        $checkListAnswerService = new checkListAnswerService($entityManager);
        $checkListFieldService = new checkListFieldService($entityManager);
        $givenAnswerService = new givenAnswerService($entityManager, $checkListFieldService, $checkListAnswerService);

        return new CheckListAjaxController(
            $entityManager,
            $checkListService,
            $checkListItemService,
            $checkListAnswerService,
            $checkListFieldService,
            $givenAnswerService
        );
    }
}