<?php
/**
 * Created by PhpStorm.
 * User: AlexanderC <self@alexanderc.me>
 * Date: 7/22/14
 * Time: 11:29
 */

namespace AlexanderC\Api\Mashery\Helpers;


use AlexanderC\Api\Mashery\InternalObjectCastInterface;
use AlexanderC\Api\Mashery\InternalObjectInterface;

class ObjectSyncer
{
    /**
     * @param InternalObjectInterface $object
     * @return null
     */
    public static function getIdentifier(InternalObjectInterface $object)
    {
        $properties = self::arrayProperties($object);

        return "member" === $object->getMasheryObjectType()
            ? (isset($properties['username']) ? $properties['username'] : null)
            : (isset($properties['id']) ? $properties['id'] : null)
        ;
    }

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

                $properties[$masheryProperty] = self::castOutgoing(
                    $object, $objectProperty, $object->$getter()
                );
            } else {
                $properties[$masheryProperty] = self::castOutgoing(
                    $object, $objectProperty, $object->{$objectProperty}
                );
            }
        }

        return array_filter($properties, function($item) {
            return is_string($item) || is_int($item) || is_bool($item) || is_array($item);
        });
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

                // be sure it is available (ex. orm entity id)
                if(method_exists($object, $setter)) {
                    $object->$setter(
                        self::castIncoming($object, $objectProperty, $data[$masheryProperty])
                    );
                }
            } else {
                $object->{$objectProperty} = self::castIncoming($object, $objectProperty, $data[$masheryProperty]);
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
        $returnProperties = [];
        $properties = $object->getMasherySyncProperties();

        foreach($properties as $objectProperty => $masheryProperty) {
            // hook to check if property is not just a normal array index
            $returnProperties[is_string($objectProperty) ? $objectProperty : $masheryProperty] = $masheryProperty;
        }

        return $returnProperties;
    }

    /**
     * @param InternalObjectInterface $object
     * @param string $property
     * @param mixed $value
     * @return mixed
     * @throws \RuntimeException
     */
    protected static function castIncoming(InternalObjectInterface $object, $property, $value)
    {
        /** @var InternalObjectCastInterface $object */
        if($object instanceof InternalObjectCastInterface) {
            $castMap = $object->masheryCastMapIncomingValue();

            if(isset($castMap[$property])) {
                if(!is_callable($castMap[$property])) {
                    throw new \RuntimeException("Mashery property cast callback should be callable");
                }

                return call_user_func($castMap[$property], $value);
            }
        }

        return $value;
    }

    /**
     * @param InternalObjectInterface $object
     * @param string $property
     * @param mixed $value
     * @return mixed
     * @throws \RuntimeException
     */
    protected static function castOutgoing(InternalObjectInterface $object, $property, $value)
    {
        /** @var InternalObjectCastInterface $object */
        if($object instanceof InternalObjectCastInterface) {
            $castMap = $object->masheryCastMapOutgoingValue();

            if(isset($castMap[$property])) {
                if(!is_callable($castMap[$property])) {
                    throw new \RuntimeException("Mashery property cast callback should be callable");
                }

                return call_user_func($castMap[$property], $value);
            }
        }

        return $value;
    }
} 