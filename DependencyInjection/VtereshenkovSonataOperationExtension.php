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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class VtereshenkovSonataOperationExtension extends Extension implements PrependExtensionInterface
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $config = $this->processConfiguration(new Configuration(), $configs);
        
        $container->setParameter('vtereshenkov_sonata_operation.use.short.classname', $config['use_short_class_name']);
        $container->setParameter('vtereshenkov_sonata_operation.exclude.from.history', $config['exclude_from_history']);
        
    }

    public function getAlias()
    {
        return 'vtereshenkov_sonata_operation';
    }

    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
        
        
        $doctrineConfig = [];
        $doctrineConfig['orm']['resolve_target_entities']['Vtereshenkov\SonataOperationBundle\Entity\Operation\UserInterface'] = $config['user_provider'];
        $doctrineConfig['orm']['resolve_target_entities']['Vtereshenkov\SonataOperationBundle\Entity\Operation\GroupInterface'] = $config['user_group_provider'];
        $doctrineConfig['orm']['mappings'][] = array(
            'name' => 'VtereshenkovSonataOperationBundle',
            'is_bundle' => true,
            'type' => 'xml',
            'prefix' => 'Vtereshenkov\SonataOperationBundle\Entity'
        );
        $container->prependExtensionConfig('doctrine', $doctrineConfig);
                
    }

}
