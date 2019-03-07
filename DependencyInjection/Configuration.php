<?php

/*
 * This file is part of the VtereshenkovSonataOperationBundle package.
 *
 * (c) Vitaliy Tereshenkov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vtereshenkov\SonataOperationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder $builder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('vtereshenkov_sonata_operation');
        $rootNode->children()
                ->scalarNode('user_provider')
//                ->isRequired()
                ->defaultValue('\App\Application\Sonata\UserBundle\Entity\User')
                ->info('User Group Entity (for example FOS\UserBundle\Model\User or Sonata\UserBundle\Entity\BaseUser)')
                ->end()
                ->scalarNode('user_group_provider')
//                ->isRequired()
                ->defaultValue('\App\Application\Sonata\UserBundle\Entity\Group')
                ->info('User Group Entity (for example FOS\UserBundle\Model\Group or Sonata\UserBundle\Entity\BaseGroup)')
                ->end()
                ->booleanNode('use_short_class_name')
                ->defaultFalse()
                ->end()
                ->arrayNode('exclude_from_history')
                ->scalarPrototype()
                ->defaultValue([])
                ->info('List of classes that are excluded from the history. Format "Vtereshenkov\ReservationBundle\Entity\Location"')
                ->end()
                ->end();

        return $builder;
    }

}
