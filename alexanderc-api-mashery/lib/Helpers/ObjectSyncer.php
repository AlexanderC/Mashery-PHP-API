<?php
/**
 * Created by PhpStorm.
 * User: AlexanderC <self@alexanderc.me>
 * Date: 7/22/14
 * Time: 11:29
 */

namespace AlexanderC\Api\Mashery\Helpers;


use AlexanderC\Api\Mashery\InternalObjectInterface;

class ObjectSyncer
{
    /**
     * @param InternalObjectInterface $object
     * @return array
     */
    public static function & arrayProperties(InternalObjectInterface $object)
    {
        $properties = [];

        foreach (self::getRealPropertiesMap($object) as $objectProperty => $masheryProperty) {
            if ($object->masheryUseSettersAndGetters()) {
                $getter = sprintf("get%s", Inflector::classify($objectProperty));

                $properties[$masheryProperty] = $object->$getter();
            } else {
                $properties[$masheryProperty] = $object->{$objectProperty};
            }
        }

        return $properties;
    }

    /**
     * @param InternalObjectInterface $object
     * @param array $data
     * @return InternalObjectInterface
     */
    public static function sync(InternalObjectInterface $object, array $data)
    {
        foreach (self::getRealPropertiesMap($object) as $objectProperty => $masheryProperty) {
            if ($object->masheryUseSettersAndGetters()) {
                $setter = sprintf("set%s", Inflector::classify($objectProperty));

                $object->$setter($data[$masheryProperty]);
            } else {
                $object->{$objectProperty} = $data[$masheryProperty];
            }
        }

        return $object;
    }

    /**
     * @param InternalObjectInterface $object
     * @return array
     */
    protected static function & getRealPropertiesMap(InternalObjectInterface $object)
    {
        $properties = $object->getMasherySyncProperties();

        foreach($properties as $objectProperty => $masheryProperty) {
            // hook to check if property is not just a normal array index
            $properties[is_string($objectProperty) ? $objectProperty : $masheryProperty] = $masheryProperty;
        }

        return $properties;
    }
} 