<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Blocks;

use DI\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Components\AccessControl\ACL;
use EnjoysCMS\Core\Components\Blocks\Blocks;
use EnjoysCMS\Core\Components\Blocks\BlockInterface;
use EnjoysCMS\Core\Components\Detector\Locations;
use EnjoysCMS\Core\Components\Helpers\HelpersBase;
use EnjoysCMS\Core\Entities\Block;
use EnjoysCMS\Core\Entities\Location;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Tests\EnjoysCMS\Traits\MockHelper;
use Tests\EnjoysCMS\Traits\ReflectionTrait;

class BlocksTest extends TestCase
{
    use MockHelper;
    use ReflectionTrait;

    public function testGetBlock()
    {
        $location = new Location();
        $location->setLocation('main');
        $this->setProperty($location, 'id', 1);

        $this->setProperty(Locations::class, 'currentLocation', $location);


        $block1 = new Block();
        $block1->setName('Block1');
        $block1->setClass('Block1');
        $block1->setLocations($location);
        $this->setProperty($block1, 'id', 1);

        $block2 = new Block();
        $block2->setName('Block2');
        $block2->setClass('Block2');
        $block2->setAlias('alias-string');
        $block2->setLocations($location);
        $this->setProperty($block2, 'id', 1);

        $block3 = new Block();
        $block3->setName('Block3');
        $block3->setClass('Block3');
        $block3->setLocations($location);
        $this->setProperty($block3, 'id', 3);

        $block4 = new Block();
        $block4->setName('Block3');
        $block4->setClass('Block3');
        $this->setProperty($block4, 'id', 4);

        $blocksInterface = $this->getMock(BlockInterface::class);
        $blocksInterface->method('view')->will(
            $this->onConsecutiveCalls('View1', 'View2')
        );

        $blockRepository = $this->getMock(EntityRepository::class);
        $blockRepository->method('find')->willReturnMap([
            [$block1->getId(), null, null, $block1],
            [$block3->getId(), null, null, $block3],
            [$block4->getId(), null, null, $block4],
        ]);
        $blockRepository->method('findOneBy')->willReturnMap([
            [['alias' => $block2->getAlias()], null, $block2],
            [['alias' => 'not-exist-block'], null, null],
        ]);

      //  var_dump($blockRepository->findOneBy([$block2->getAlias()]));

        $container = $this->getMock(Container::class);

        HelpersBase::setContainer($container);

        $em = $this->getMock(EntityManager::class);

        $em->method('getRepository')->willReturn($blockRepository);
        $container->method('make')->willReturnMap([
            [$block1->getClass(), ['block' => $block1], $blocksInterface],
            [$block2->getClass(), ['block' => $block2], $blocksInterface],
            [$block3->getClass(), ['block' => $block3], $blocksInterface],
            [$block4->getClass(), ['block' => $block4], $blocksInterface],
        ]);

        $acl = $this->getMock(ACL::class);
        $acl->method('access')->willReturnMap([
            [$block1->getBlockActionAcl(), ":Блок: Доступ к просмотру блока '{$block1->getName()}'", true],
            [$block2->getBlockActionAcl(), ":Блок: Доступ к просмотру блока '{$block2->getName()}'", true],
            [$block3->getBlockActionAcl(), ":Блок: Доступ к просмотру блока '{$block3->getName()}'", false],
            [$block4->getBlockActionAcl(), ":Блок: Доступ к просмотру блока '{$block4->getName()}'", true],
        ]);

        $logger = $this->getMock(NullLogger::class);
        $logger->expects($this->exactly(2))->method('debug');
        $logger->expects($this->exactly(1))->method('notice');

        $container->method('get')->willReturnMap([
            [EntityManager::class, $em],
            [LoggerInterface::class, $logger],
            [ACL::class, $acl]
        ]);
        $blocks = new Blocks($container);

        $this->assertNull($blocks->getBlock('not-exist-block'));

        $this->assertSame('View1', $blocks->getBlock($block1->getId()));
        $this->assertSame('View2', $blocks->getBlock($block2->getAlias()));
        $this->assertSame(null, $blocks->getBlock($block3->getId()));
        $this->assertSame(null, $blocks->getBlock($block4->getId()));



    }
}
