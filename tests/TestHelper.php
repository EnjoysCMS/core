<?php

declare(strict_types=1);


namespace Tests\EnjoysCMS;


use EnjoysCMS\Core\Entities\ACL;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Routing\Route;

final class TestHelper
{
    public static function getAclEntities(ACL|MockObject $entity, array $data): array
    {
        $result = [];
        $i = 1;
        foreach ($data as $action => $comment) {
            $aclEntity = clone $entity;
            $aclEntity->method('getId')->willReturn($i);
            $aclEntity->method('getController')->willReturn($action);
            $aclEntity->method('getComment')->willReturn($comment);
            $result[$action] = $aclEntity;
            $i++;
        }

        return $result;
    }

    public static function getRoutes(Route|MockObject $entity, array $data): array
    {
        $result = [];
        foreach ($data as $action => $comment) {
            $route = clone $entity;
            $route->method('getDefault')->willReturn($action);
            $route->method('getOption')->willReturn($comment);
            $result[$action] = $route;
        }
        return $result;
    }
}
