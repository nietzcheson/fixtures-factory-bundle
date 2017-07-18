<?php

namespace Nietzscheson\FixturesFactoryBundle\Fixtures;

use Hautelook\AliceBundle\BundleResolverInterface;
use Symfony\Component\HttpKernel\Kernel;
use Hautelook\AliceBundle\FixtureLocatorInterface;
use Symfony\Component\Yaml\Yaml;

class ArrayResolver implements ArrayResolverInterface
{
    /**
     * @var Kernel $kernel
     */
    private $kernel;

    /**
     * @var array $bundles
     */
    private $bundles;

    /**
     * @var FixtureLocatorInterface $localtor
     */
    private $locator;

    /**
     * @param Kernel $kernel
     * @param array $bundles
     * @param $locator
     */
    public function __construct(Kernel $kernel, array $bundles, $locator)
    {
        $this->kernel = $kernel;
        $this->bundles = $bundles;
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($fixture, $id, $data = array())
    {
        return $this->getFixture($fixture, $id, $data);
    }

    private function getFixture($entity, $id, $data)
    {
        $princialFixture = [$entity => [$id => array_merge($this->getFixtures()[$entity][$id], $data)]];

        return $this->relatedFixtures($princialFixture);
    }

    public function getFixturesById()
    {
        $fixtures = [];

        foreach($this->getFixtures() as $f => $fixture){
            foreach($fixture as $i => $item){
                $fixtures[$i] = [$f => [$i => $item]];
            }
        }

        return $fixtures;
    }

    private function getFixtures()
    {
        $fixtures = $this->locator->locateFiles($this->kernel->getBundles(), 'prod');

        $values = [];

        foreach($fixtures as $fixtureValue){
            $values += Yaml::parse(file_get_contents($fixtureValue));
        }

        return $values;
    }

    private function relatedFixtures($principalFixture)
    {
        $fixturesRelated = [];
        
        foreach($principalFixture as $i => $items){

            foreach($items[key($items)] as $item){

                if(!is_array($item)){
                    if(preg_match('[^@]', $item)){
                        $fixturesRelated[] = str_replace('@', '', $item);
                    }
                }

            }

        }

        if(!$fixturesRelated){
            return $principalFixture;
        }

        $fixtures = [];

        $childFixtures = [];

        foreach($fixturesRelated as $item){

            foreach($this->getFixturesById() as $f => $fixture){
                if($f == $item){
                    $fixtures += $fixture;

                    $childFixtures = $this->resolve(key($fixture), $f, []);
                }
            }
        }

        $fixtures = array_merge($fixtures, $principalFixture);

        if($childFixtures){
            $fixtures = array_merge($fixtures, $childFixtures);
        }

        return $fixtures;
    }
}