<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log';
    }

    public function getProjectDir(): string
    {
        return parent::getProjectDir().'/Tests/app';
    }

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';

        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $confDir = $this->getProjectDir().'/config';

        $container->import($confDir.'/packages/*.yaml');
        $container->import($confDir.'/packages/'.$this->environment.'/*.yaml', null, true);
        $container->import($confDir.'/packages/'.$this->environment.'/**/*.yaml', null, true);
        $container->import($confDir.'/services.yaml');
        $container->import($confDir.'/services_'.$this->environment.'.yaml', null, true);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/routes/*.yaml');
        $routes->import($confDir.'/routes/'.$this->environment.'/*.yaml', null, true);
        $routes->import($confDir.'/routes/'.$this->environment.'/**/*.yaml', null, true);
        $routes->import($confDir.'/routes.yaml');
    }
}
