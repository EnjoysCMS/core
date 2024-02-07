<?php

use Doctrine\Common\Annotations\AnnotationReader;
use EnjoysCMS\Core\AccessControl\AccessControlManage;
use EnjoysCMS\Core\AccessControl\ACL\ACLManage;
use EnjoysCMS\Core\Auth\Identity;
use EnjoysCMS\Core\Auth\IdentityInterface;
use EnjoysCMS\Core\Auth\TokenStorage\DatabaseTokenStorage;
use EnjoysCMS\Core\Auth\TokenStorageInterface;
use EnjoysCMS\Core\Auth\UserStorage\DatabaseUserStorage;
use EnjoysCMS\Core\Auth\UserStorageInterface;
use EnjoysCMS\Core\Block;
use EnjoysCMS\Core\Extensions\Composer\Utils;
use EnjoysCMS\Core\Modules\Module;
use EnjoysCMS\Core\Modules\ModuleCollection;
use Invoker\Invoker;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Cache\ItemInterface;

return [

    'BlocksAndWidgetsFinder' => DI\factory(
        function () {
            $finder = new Finder();
            $finder->files()
                ->in([
                    $_ENV['APP_DIR'],
                    getenv('ROOT_PATH') . '/modules',
                ])
                ->exclude([
                    'node_modules',
                    'vendor',
                    'tests'
                ]);
            return $finder;
        }
    ),

    //Blocks
    Block\BlockCollection::class => DI\factory(
        function (ContainerInterface $container) {
            $cache = new FilesystemAdapter(directory: $_ENV['TEMP_DIR'] . '/cache/blocks');
            return $cache->get('blocks', function (ItemInterface $item) use ($container) {
                $item->expiresAfter(10);

                $loader = new Block\Loader\BlockLoader(
                    $container->get('BlocksAndWidgetsFinder'),
                    new AnnotationReader()
                );
                $collection = new Block\BlockCollection();
                $collection->addCollection($loader->getCollection());
                return $collection;
            });
        }
    ),

    Block\WidgetCollection::class => DI\factory(
        function (ContainerInterface $container) {
            $cache = new FilesystemAdapter(directory: $_ENV['TEMP_DIR'] . '/cache/blocks');
            return $cache->get('blocks', function (ItemInterface $item) use ($container) {
                $item->expiresAfter(10);

                $loader = new Block\Loader\WidgetLoader(
                    $container->get('BlocksAndWidgetsFinder'),
                    new AnnotationReader()
                );
                $collection = new Block\WidgetCollection();
                $collection->addCollection($loader->getCollection());
                return $collection;
            });
        }
    ),

    // Modules
    ModuleCollection::class => DI\factory(
        function () {
            $cache = new FilesystemAdapter(directory: $_ENV['TEMP_DIR'] . '/cache/modules');
            return $cache->get('modules', function (ItemInterface $item) {
                $item->expiresAfter(1);
                $finder = new Finder();
                $finder->files()->in(getenv('ROOT_PATH') . '/modules');
                $finder->name('composer.json')->depth(1);


                $moduleCollection = new ModuleCollection();

                foreach ($finder as $item) {
                    $moduleCollection->addModule(new Module(Utils::parseComposerJson($item->getPathname())));
                }

                return $moduleCollection;
            });
        }
    ),

    UserStorageInterface::class => DI\get(
        DatabaseUserStorage::class
    ),

    IdentityInterface::class => DI\get(Identity::class),

    TokenStorageInterface::class => DI\get(
        DatabaseTokenStorage::class
    ),

    InvokerInterface::class => DI\factory(
        function (ContainerInterface $container) {
            return new Invoker(container: $container);
        }
    ),

    AccessControlManage::class => DI\get(ACLManage::class)
];

