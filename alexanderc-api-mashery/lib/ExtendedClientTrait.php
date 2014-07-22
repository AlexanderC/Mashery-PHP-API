<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;


use AlexanderC\Api\Mashery\Exception\UnknownObjectTypeException;
use AlexanderC\Api\Mashery\Helpers\ObjectSyncer;

trait ExtendedClientTrait
{
    /**
     * @param string|InternalObjectInterface $objectType
     * @param array $parameters
     * @return Response
     */
    public function fetch($objectType, array $parameters = [])
    {
        return $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'fetch', true, true)
            : $this->execute($objectType, 'fetch', $parameters);
    }

    /**
     * @param string|InternalObjectInterface $objectType
     * @param array $parameters
     * @return Response
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
     * @return Response
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
     * @return Response
     */
    public function delete($objectType, array $parameters = [])
    {
        return $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'delete', false, true)
            : $this->execute($objectType, 'delete', $parameters);
    }

    /**
     * @param string|InternalObjectInterface $objectType
     * @param array $parameters
     * @return Response
     */
    public function validate($objectType, array $parameters = [])
    {
        $response = $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'validate', false)
            : $this->execute($objectType, 'validate', $parameters);

        return $response;
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
     * @param bool $onlyIdentifier
     * @return Response|null
     */
    protected function executeFromObject(
        InternalObjectInterface $object, $type,
        $withSync = true, $onlyIdentifier = false)
    {
        $this->validateObjectType($object->getMasheryObjectType());
        $response = null;

        $response = new Response(
            $this->call(
                sprintf("%s.%s", $object->getMasheryObjectType(), $type),
                [$onlyIdentifier ? ObjectSyncer::getIdentifier($object) : ObjectSyncer::arrayProperties($object)]
            )
        );

        if($withSync && !$response->isError()) {
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