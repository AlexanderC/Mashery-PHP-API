<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;


use AlexanderC\Api\Mashery\Exception\UnknownObjectTypeException;

trait ExtendedClientTrait
{
    /**
     * @param string $objectType
     * @param array $parameters
     * @return mixed
     */
    public function fetch($objectType, array $parameters = [])
    {
        if ($objectType instanceof InternalObjectInterface) {
            $this->validateObjectType($objectType->getMasheryObjectType());

            foreach ($objectType->getMasherySyncProperties() as $property) {
                if ($objectType->masheryUseSettersAndGetters()) {
                    $getter = sprintf("get%s", Inflector::classify($property));

                    $parameters[$property] = $objectType->$getter();
                } else {
                    $parameters[$property] = $objectType->{$property};
                }
            }

            $response = new Response(
                $this->call(sprintf("%s.fetch", $objectType->getMasheryObjectType()), $parameters)
            );

            $response->sync($objectType);

            return $response;
        } else {
            $this->validateObjectType($objectType);

            return new Response($this->call(sprintf("%s.fetch", $objectType), $parameters));
        }
    }

    /**
     * @param string $objectType
     * @param array $parameters
     * @return mixed
     */
    public function create($objectType, array $parameters = [])
    {
        if ($objectType instanceof InternalObjectInterface) {
            $this->validateObjectType($objectType->getMasheryObjectType());

            foreach ($objectType->getMasherySyncProperties() as $property) {
                if ($objectType->masheryUseSettersAndGetters()) {
                    $getter = sprintf("get%s", Inflector::classify($property));

                    $parameters[$property] = $objectType->$getter();
                } else {
                    $parameters[$property] = $objectType->{$property};
                }
            }

            $response = new Response(
                $this->call(sprintf("%s.create", $objectType->getMasheryObjectType()), $parameters)
            );

            $response->sync($objectType);

            return $response;
        } else {
            $this->validateObjectType($objectType);

            return new Response($this->call(sprintf("%s.create", $objectType), $parameters));
        }
    }

    /**
     * @param string $objectType
     * @param array $parameters
     * @return mixed
     */
    public function update($objectType, array $parameters = [])
    {
        if ($objectType instanceof InternalObjectInterface) {
            $this->validateObjectType($objectType->getMasheryObjectType());

            foreach ($objectType->getMasherySyncProperties() as $property) {
                if ($objectType->masheryUseSettersAndGetters()) {
                    $getter = sprintf("get%s", Inflector::classify($property));

                    $parameters[$property] = $objectType->$getter();
                } else {
                    $parameters[$property] = $objectType->{$property};
                }
            }

            $response = new Response(
                $this->call(sprintf("%s.update", $objectType->getMasheryObjectType()), $parameters)
            );

            $response->sync($objectType);

            return $response;
        } else {
            $this->validateObjectType($objectType);

            return new Response($this->call(sprintf("%s.update", $objectType), $parameters));
        }
    }

    /**
     * @param string $objectType
     * @param array $parameters
     * @return mixed
     */
    public function delete($objectType, array $parameters = [])
    {
        if ($objectType instanceof InternalObjectInterface) {
            $this->validateObjectType($objectType->getMasheryObjectType());

            foreach ($objectType->getMasherySyncProperties() as $property) {
                if ($objectType->masheryUseSettersAndGetters()) {
                    $getter = sprintf("get%s", Inflector::classify($property));

                    $parameters[$property] = $objectType->$getter();
                } else {
                    $parameters[$property] = $objectType->{$property};
                }
            }

            $response = new Response(
                $this->call(sprintf("%s.delete", $objectType->getMasheryObjectType()), $parameters)
            );

            $response->sync($objectType);

            return $response;
        } else {
            $this->validateObjectType($objectType);

            return new Response($this->call(sprintf("%s.delete", $objectType), $parameters));
        }
    }

    /**
     * @param string $objectType
     * @param array $parameters
     * @param array $error
     * @return bool
     */
    public function validate($objectType, array $parameters = [], &$error)
    {
        if ($objectType instanceof InternalObjectInterface) {
            $this->validateObjectType($objectType->getMasheryObjectType());

            foreach ($objectType->getMasherySyncProperties() as $property) {
                if ($objectType->masheryUseSettersAndGetters()) {
                    $getter = sprintf("get%s", Inflector::classify($property));

                    $parameters[$property] = $objectType->$getter();
                } else {
                    $parameters[$property] = $objectType->{$property};
                }
            }

            $response = new Response(
                $this->call(sprintf("%s.validate", $objectType->getMasheryObjectType()), $parameters)
            );

            $error = $response->getError();

            return true === $response->getResult();
        } else {
            $this->validateObjectType($objectType);

            $response = new Response($this->call(sprintf("%s.validate", $objectType), $parameters));

            $error = $response->getError();

            return true === $response->getResult();
        }
    }

    /**
     * @param mixed $objectType
     * @throws Exception\UnknownObjectTypeException
     */
    protected function validateObjectType($objectType)
    {
        $isValidObject = !is_string($objectType) || in_array($objectType, $this->definition->getObjectTypes());

        if (!$isValidObject) {
            throw new UnknownObjectTypeException("Unknown object type provided");
        }
    }
} 