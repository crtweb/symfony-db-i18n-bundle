<?php
/**
 * 23.03.2020
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\Tests;

use Creative\DbI18nBundle\DbI18nBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @inheritDoc
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new DbI18nBundle(),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function configureContainer(\Symfony\Component\DependencyInjection\ContainerBuilder $c, \Symfony\Component\Config\Loader\LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/doctrine.yaml');
        $loader->load(__DIR__ . '/../src/Resources/config', 'glob');
    }
}
