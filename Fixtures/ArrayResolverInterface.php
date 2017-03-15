<?php

namespace Nietzcheson\FixturesFactoryBundle\Fixtures;

interface ArrayResolverInterface
{

    /**
     * @param string $fixture
     * @param string $id
     * @param array $data
     *
     * @return object
     */
    public function resolve($fixture, $id, $data = array());

}