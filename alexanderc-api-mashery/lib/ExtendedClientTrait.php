<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;


use AlexanderC\Api\Mashery\Exception\UnknownObjectTypeException;
use AlexanderC\Api\Mashery\Helpers\ObjectSyncer;
use AlexanderC\Api\Mashery\Response;

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
            ? $this->executeFromObject($objectType, 'update', false, false, true)
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
     * @param bool $preUpdate
     * @param array $parameters
     * @return Response|null
     */
    public function validate($objectType, $preUpdate = false, array $parameters = [])
    {
        $response = $objectType instanceof InternalObjectInterface
            ? $this->executeFromObject($objectType, 'validate', false, false, (bool) $preUpdate)
            : $this->execute($objectType, 'validate', $parameters);

        // TODO: figure out what's with mashery response!
        // returned result differes from docs:
        // http://support.mashery.com/docs/read/mashery_api/20/Validating_Fields
        $responseResult = $response->getResult();
        if(!$response->isError() && true !== $responseResult) {
            return new Response(
                [
                    'result' => null,
                    'error' => [
                        'message' => 'Invalid Object',
                        'code' => 1000,
                        'data' => $responseResult
                    ],
                    'id' => $response->getId()
                ]);
        }

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
     * @param array $additionalOptions
     * @return Response
     */
    public function execute($objectType, $type, array $parameters = [], array $additionalOptions = null)
    {
        $this->validateObjectType($objectType);

        if(null === $additionalOptions) {
            return new Response($this->call(sprintf("%s.%s", $objectType, $type), $parameters));
        }

        return new Response($this->call(sprintf("%s.%s", $objectType, $type), $parameters, $additionalOptions));
    }

    /**
     * @param InternalObjectInterface $object
     * @param string $type
     * @param bool $withSync
     * @param bool $onlyIdentifier
     * @param bool $skipUpdateFields
     * @return Response|null
     */
    protected function executeFromObject(
        InternalObjectInterface $object, $type,
        $withSync = true, $onlyIdentifier = false,
        $skipUpdateFields = false)
    {
        $this->validateObjectType($object->getMasheryObjectType());
        $response = null;

        $parameters = null;

        if($onlyIdentifier) {
            $parameters = ObjectSyncer::getIdentifier($object);
        } else {
            $parameters = ObjectSyncer::arrayProperties($object, $skipUpdateFields);
        }

        $response = new Response(
            $this->call(
                sprintf("%s.%s", $object->getMasheryObjectType(), $type),
                [$parameters]
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