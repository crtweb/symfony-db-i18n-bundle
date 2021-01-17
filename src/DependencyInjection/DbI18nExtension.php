<?php
/**
 * 2019-04-19.
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Class DbI18nBundleExtension.
 *
 * @package Creative\DbI18nBundle\DependencyInjection
 */
class DbI18nExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('db_i18n.entity', $config['entity']);
        $container->setParameter('db_i18n.domain', $config['domain']);
        $container->setParameter('db_i18n.root_dir', __DIR__ . '/../');
        $container->setParameter('db_i18n.translation_dir', \dirname($container->getParameter('kernel.cache_dir')));

        $localeNames = [$container->getParameter('kernel.default_locale')];
        if ($container->hasParameter('locales') && is_array($locales = $container->getParameter('locales'))) {
            $localeNames = $locales;
        }
        $this->makeLocaleFiles($localeNames, $container->getParameter('db_i18n.translation_dir'), $container->getParameter('db_i18n.domain'));

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('config.yaml');
    }

    /**
     * @param array  $locales
     * @param string $targetDir
     * @param string $domain
     */
    protected function makeLocaleFiles(array $locales, string $targetDir, string $domain): void
    {
        foreach ($locales as $locale) {
            $path = $targetDir . '/' . $domain . '.' . $locale . '.db';
            if (!is_file($path)) {
                touch($path);
            }
        }
    }
}
