<?php

namespace Nietzcheson\FixturesFactoryBundle\Fixtures;

interface FactoryInterface
{
    /**
     * @param string $entity
     * @param string $entityId
     * @param array $data
     *
     * @return object
     */
    public function create($entity, $entityId, array $data);
}