<?php

namespace EnjoysCMS\Core\Extensions\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class EnumType extends Type
{
    protected $name;
    protected $values = array();

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $values = array_map(
            function ($val) {
                return "'" . $val . "'";
            },
            $this->values
        );

        return "ENUM(" . implode(", ", $values) . ")";
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        return $value;
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (!in_array($value, $this->values)) {
            throw new \InvalidArgumentException("Invalid '" . $this->name . "' value.");
        }
        return $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
