<?php

namespace Proengeno\Edifact\Message;

use Proengeno\Edifact\Exceptions\SegmentDesciptionException;

class SegmentDescription
{
    public static $instanceLookup = [];

    private $jsonPath;
    private $dataKey;

    private function __construct($jsonPath = null)
    {
        $this->json = $jsonPath ? json_decode(file_get_contents($jsonPath), 1) : [];
    }

    public function make($jsonPath = null)
    {
        if (!isset(self::$instanceLookup[$jsonPath])) {
            self::$instanceLookup[$jsonPath] = new self($jsonPath);
        }
        return self::$instanceLookup[$jsonPath];
    }

    public function description($groupKey, $dataKey, $valueKey)
    {
        return $this->getValues('description', $groupKey, $dataKey, $valueKey);
    }

    public function name($groupKey, $dataKey, $valueKey)
    {
        return $this->getValues('name', $groupKey, $dataKey, $valueKey);
    }

    public function tags($groupKey, $dataKey, $valueKey)
    {
        return $this->getValues('tags', $groupKey, $dataKey, $valueKey);
    }

    public function all()
    {
        return $this->json;
    }

    public function groupKeys()
    {
        return array_keys($this->json);
    }

    public function dataKeys($groupKey)
    {
        $this->checkGroupKey($groupKey);

        return array_keys($this->json[$groupKey]);
    }

    public function valueKeys($groupKey, $dataKey)
    {
        $this->checkDataKey($groupKey, $dataKey);

        return array_keys($this->json[$groupKey][$dataKey]['values']);
    }

    public function taggedKeys($groupKey, $dataKey, array $finder)
    {
        return array_keys(array_filter($this->json[$groupKey][$dataKey]['values'], function($item) use ($finder) {
            return !! array_intersect($item['tags'], $finder);
        }));
    }

    private function getValues($catergory, $groupKey, $dataKey, $valueKey)
    {
        $this->checkDataKey($groupKey, $dataKey);

        $this->checkValue($groupKey, $dataKey, $valueKey, $catergory);

        return $this->json[$groupKey][$dataKey]['values'][$valueKey][$catergory];
    }

    private function checkValue($groupKey, $dataKey, $valueKey, $catergory)
    {
        $this->checkDataKey($groupKey, $dataKey);

        if (!isset($this->json[$groupKey][$dataKey]['values'][$valueKey])) {
            throw new SegmentDesciptionException("No '$valueKey' available for Dataset '$groupKey/$dataKey'.");
        }
    }

    private function checkDataKey($groupKey, $dataKey)
    {
        $this->checkGroupKey($groupKey);

        if (!isset($this->json[$groupKey][$dataKey])) {
            throw new SegmentDesciptionException("No DataKey '$dataKey' available.");
        }
    }

    private function checkGroupKey($groupKey)
    {
        if (!isset($this->json[$groupKey])) {
            throw new SegmentDesciptionException("No '$groupKey' available.");
        }
    }
}
