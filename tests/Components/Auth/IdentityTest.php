<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Auth;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Components\Auth\Authorize;
use EnjoysCMS\Core\Components\Auth\AuthorizedData;
use EnjoysCMS\Core\Components\Auth\Identity;
use EnjoysCMS\Core\Entities\User;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Tests\EnjoysCMS\Traits\MockHelper;

class IdentityTest extends TestCase
{

    use MockHelper;

    private \Faker\Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testGetUser()
    {
        $userEntity = new User();
        $userEntity->setName($userName = $this->faker->name);
        $userEntity->setEmail($userEmail = $this->faker->email);
        $userEntity->setLogin($userLogin =  $this->faker->userName);
        $userEntity->setPasswordHash($userPasswordHash = $this->faker->md5);
        $userEntity->setEditable($userEditable = $this->faker->boolean);

        $em = $this->getMock(EntityManager::class);
        $userRepository = $this->getMock(EntityRepository::class);

        $authorize = $this->getMock(Authorize::class);

        $userRepository->method('find')->willReturn($userEntity);
        $em->method('getRepository')->willReturn($userRepository);
        $authorize->method('getAuthorizedData')->willReturn(new AuthorizedData(42));

        $identity = new Identity($em, $authorize);

        $this->assertSame($userName, $identity->getUser()->getName());
        $this->assertSame($userEmail, $identity->getUser()->getEmail());
        $this->assertSame($userLogin, $identity->getUser()->getLogin());
        $this->assertSame($userPasswordHash, $identity->getUser()->getPasswordHash());
        $this->assertSame($userEditable, $identity->getUser()->isEditable());
        $this->assertSame([], $identity->getUser()->getAclAccessIds());
    }
}
