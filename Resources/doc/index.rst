
Getting Started With VtereshenkovSonataOperationBundle
========================================

This bundle provides a way for save history user actions in Sonata Admin Bundle.

Prerequisites
-------------

This version of the bundle requires Symfony 4.0+ and 
sonata-project/admin-bundle 3.35+, sonata-project/doctrine-orm-admin-bundle 3.6+.


Installation
------------

Installation process:

1. Download VtereshenkovSonataOperationBundle using composer
2. Enable the Bundle
3. Create your Sonata Admin class (CityAdmin, ClientAdmin or whatever)
4. Configure the VtereshenkovSonataOperationBundle
5. Update your database schema


Step 1: Download MsalsasVotingBundle using composer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add bundle repository in you composer.json

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vtereshenkov/sonata-operation-bundle"
        }
    ]

Require the bundle with composer:

.. code-block:: bash

    $ composer require vtereshenkov/sonata-operation-bundle

Composer will install the bundle to your project's ``vendor/vtereshenkov/sonata-operation-bundle`` directory.


Step 2: Enable the bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    If you're using Flex, this is done automatically

Enable the bundle in the kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Vtereshenkov\SonataOperationBundle\SonataOperationBundle(),
            // ...
        );
    }


Step 3: Create your Sonata Admin class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The goal of this bundle is to handle sonata admin events for a ``create``, ``update``, ``remove`` actions and persist object data (before and after) to a database (MySql).
Your first job, then, is to create the ``Sonata Admin`` any class (class must extend Sonata\AdminBundle\Admin\AbstractAdmin)
for your application. This class can look and act however you want: add any
properties or methods you find useful. 


Step 4: Configure the VtereshenkovSonataOperationBundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    If you're using Flex, this is done automatically

Add the following configuration to your ``config/packages/vtereshenkov_sonata_operation.yaml`` file.

.. configuration-block::

    .. code-block:: yaml

        # config/packages/vtereshenkov_sonata_operation.yaml
        vtereshenkov_sonata_operation:
            user_provider: \App\Application\Sonata\UserBundle\Entity\User # Your ``User`` class which the implements Symfony\Component\Security\Core\User\UserInterface
            user_provider: \App\Application\Sonata\UserBundle\Entity\Group # Your ``UserGroup`` class which the implements FOS\UserBundle\Model\GroupInterface
            


Step 5: Update your database schema
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Now that the bundle is configured, the last thing you need to do is update your
database schema.

Run the following command.

.. code-block:: bash

    $ php bin/console doctrine:schema:update --force

