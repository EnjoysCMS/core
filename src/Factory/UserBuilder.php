<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Factory;


use Doctrine\ORM\EntityManagerInterface;
use EnjoysCMS\Core\Users\Entity\Group;
use EnjoysCMS\Core\Users\Entity\User;
use EnjoysCMS\Core\Users\Repository\GroupRepository;

final class UserBuilder
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GroupRepository $groupRepository
    ) {
    }

    public function create(
        string $login,
        string $pass,
        string $name,
        string $email,
        string|Group $group
    ): User {

        if (is_string($group)){
            $groupName = $group;
            $group = $this->groupRepository->find($group);
            if ($group === null ){
                $group = new Group($groupName);
                $group->setStatus(1);
                $this->em->persist($group);
            }
        }

        $user = new User();
        $user->setName($name);
        $user->setLogin($login);
        $user->setEmail($email);
        $user->genAndSetPasswordHash($pass);
        $user->setGroups($group);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

}
