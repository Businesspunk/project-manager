<?php

namespace App\Model\Work\Entity\Projects\Role;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class PermissionsType extends JsonType
{
    public const NAME = 'work_projects_role_permissions';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof ArrayCollection) {
            if ($value->count() > 0) {
                $data = array_map([self::class, 'serialize'], $value->toArray());
            } else {
                $data = null;
            }
        } else {
            $data = $value;
        }

        return parent::convertToDatabaseValue($data, $platform);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!is_array($data = parent::convertToPHPValue($value, $platform))) {
            return new ArrayCollection();
        }

        $filtered = $this->filterExistedNowValues($data);
        return new ArrayCollection(array_map([self::class, 'deserialize'], $filtered));
    }

    public function getName()
    {
        return self::NAME;
    }

    private function filterExistedNowValues(array $values)
    {
        return array_filter($values, function ($e) {
            return in_array($e, Permission::getNames());
        });
    }

    private static function serialize(Permission $permission): string
    {
        return $permission->getName();
    }

    private static function deserialize(string $permission): Permission
    {
        return new Permission($permission);
    }
}
