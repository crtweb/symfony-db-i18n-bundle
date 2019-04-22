<?php
/**
 * 2019-04-19.
 */

declare(strict_types=1);

namespace Creative\DbI18nBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @package Creative\DbI18nBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('creative_db_i18n');
        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('entity')->defaultValue('Creative\\DbI18nBundle\\Entity\\Translation')->end()
            ->scalarNode('domain')->defaultValue('db_messages')->end()
        ;

        return $treeBuilder;
    }
}
