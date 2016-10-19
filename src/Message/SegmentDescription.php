<?php

namespace Proengeno\Edifact\Message;

use Proengeno\Edifact\Exceptions\SegmentDesciptionException;

class SegmentDescription
{
    public static $instanceLookup = [];

    private $jsonPath;
    private $method;

    private function __construct($jsonPath = null)
    {
        $this->json = $jsonPath ? json_decode(file_get_contents($jsonPath), 1) : '{}';
    }

    public function make($jsonPath = null)
    {
        if (!isset(self::$instanceLookup[$jsonPath])) {
            self::$instanceLookup[$jsonPath] = new self($jsonPath);
        }
        return self::$instanceLookup[$jsonPath];
    }

    public function description($method, $key)
    {
        return $this->getValues('description', $method, $key);
    }

    public function name($method, $key)
    {
        return $this->getValues('name', $method, $key);
    }

    public function tags($method, $key)
    {
        return $this->getValues('tags', $method, $key);
    }

    public function keys($method)
    {
        $this->checkMethod($method, 'Tags');

        return array_keys($this->json[$method]['values']);
    }

    public function taggedKeys($method, array $finder)
    {
        return array_keys(array_filter($this->json[$method]['values'], function($item) use ($finder) {
            return !! array_intersect($item['tags'], $finder);
        }));
    }

    private function getValues($catergory, $method, $key)
    {
        $this->checkMethod($method, $catergory);

        $this->checkValue($method, $key, $catergory);

        return $this->json[$method]['values'][$key][$catergory];
    }

    private function checkMethod($method, $catergory)
    {
        if (!isset($this->json[$method])) {
            throw new SegmentDesciptionException("No '$catergory' available for Method named '$method'.");
        }
    }

    private function checkValue($method, $key, $catergory)
    {
        if (!isset($this->json[$method]['values'][$key])) {
            throw new SegmentDesciptionException("No '$key' available for '$method'.");
        }
    }
}
