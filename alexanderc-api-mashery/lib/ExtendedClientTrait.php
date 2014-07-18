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
     * @param string|InternalObjectInterface $objectType
     * @param array $parameters
     * @return mixed
     */
    public function fetch($objectType, array $parameters = [])
    {
        return $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'fetch')
            : $this->execute($objectType, 'fetch', $parameters);
    }

    /**
     * @param string|InternalObjectInterface $objectType
     * @param array $parameters
     * @return mixed
     */
    public function create($objectType, array $parameters = [])
    {
        return $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'create')
            : $this->execute($objectType, 'create', $parameters);
    }

    /**
     * @param string|InternalObjectInterface $objectType
     * @param array $parameters
     * @return mixed
     */
    public function update($objectType, array $parameters = [])
    {
        return $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'update')
            : $this->execute($objectType, 'update', $parameters);
    }

    /**
     * @param string|InternalObjectInterface $objectType
     * @param array $parameters
     * @return mixed
     */
    public function delete($objectType, array $parameters = [])
    {
        return $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'delete')
            : $this->execute($objectType, 'delete', $parameters);
    }

    /**
     * @param string|InternalObjectInterface $objectType
     * @param array $parameters
     * @param Response $response
     * @return bool
     */
    public function validate($objectType, array $parameters = [], &$response)
    {
        $response = $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'validate', false)
            : $this->execute($objectType, 'validate', $parameters);

        return true === $response->getResult();
    }

    /**
     * @param string|MsrQL $query
     * @return QueryResponse
     */
    public function query($query)
    {
        return new QueryResponse($this->call('object.query', [(string) $query]));
    }

    /**
     * @param string $objectType
     * @param string $type
     * @param array $parameters
     * @return Response
     */
    protected function execute($objectType, $type, array $parameters = [])
    {
        $this->validateObjectType($objectType);

        return new Response($this->call(sprintf("%s.%s", $objectType, $type), $parameters));
    }

    /**
     * @param InternalObjectInterface $object
     * @param string $type
     * @param bool $withSync
     * @return Response
     */
    protected function executeFromObject($object, $type, $withSync = true)
    {
        $this->validateObjectType($object->getMasheryObjectType());

        foreach ($object->getMasherySyncProperties() as $property) {
            if ($object->masheryUseSettersAndGetters()) {
                $getter = sprintf("get%s", Inflector::classify($property));

                $parameters[$property] = $object->$getter();
            } else {
                $parameters[$property] = $object->{$property};
            }
        }

        $response = new Response(
            $this->call(sprintf("%s.%s", $object->getMasheryObjectType(), $type), $parameters)
        );

        if($withSync) {
            $response->sync($object);
        }

        return $response;
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