<?php
namespace CheckList\Controller\Factory;

use Interop\Container\ContainerInterface;
use YouTube\Controller\IndexController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use YouTube\Service\youTubeService;
/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $youTubeService = new youTubeService($entityManager);
        return new IndexController($entityManager, $youTubeService);
    }
}