<?php

namespace Nietzcheson\FixturesFactoryBundle\Fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\DataLoaderInterface;

class Factory implements FactoryInterface
{

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var ArrayResolver $fixturesArray
     */
    private $fixturesArray;

    /**
     * @var DataLoaderInterface $loader
     */
    private $loader;

    /**
     * @param EntityManagerInterface $em
     * @param ArrayResolver $fixturesArray
     * @param DataLoaderInterface $loader
     */
    public function __construct(EntityManagerInterface $em, ArrayResolver $fixturesArray, DataLoaderInterface $loader)
    {
        $this->em = $em;
        $this->fixturesArray = $fixturesArray;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function create($class, $id, array $data)
    {
        $fixture = $this->fixturesArray->resolve($this->em->getClassMetadata($class)->getName(), $id, $data);

        $data = $this->loader->loadData($fixture)->getObjects();

        $this->persister($data);

        return $data[$id];
    }

    private function persister($data)
    {
        foreach($data as $item){
            $this->em->persist($item);
        }

        $this->em->flush();
    }

}