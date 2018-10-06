<?php
declare(strict_types=1);


namespace Kemoc\Storehouse\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kemoc_storehouse_api');
        $rootNode->children()
            ->booleanNode('enabled')
                ->defaultTrue()->end()
            ->end();

        return $treeBuilder;
    }
}
