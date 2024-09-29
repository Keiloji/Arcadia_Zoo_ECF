<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    // Configure le conteneur de services
    protected function configureContainer(ContainerConfigurator $container): void
    {
        // Importer tous les fichiers de configuration dans packages
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml');

        // Importer les fichiers de configuration des services
        $container->import('../config/{services}.yaml');
        $container->import('../config/{services}_'.$this->environment.'.yaml');
    }

    // Configure les routes
    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // Importer tous les fichiers de configuration dans routes
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
    }
}

