<?php

namespace Code\Packages\Edifact\tests\Message;

use Proengeno\Edifact\Test\TestCase;
use Proengeno\Edifact\Message\SegmentDescription;
use Proengeno\Edifact\Exceptions\SegmentDesciptionException;

class SegmentDescriptionTest extends TestCase
{
    private $segDescription;

    protected function setUp()
    {
        $this->segDescription = SegmentDescription::make(__DIR__ . '/../Fixtures/Segments/meta/dummy.json');
    }

    /**
     * @test
     * @dataProvider additionProvider
     */
    public function it_returns_the_meta_attributes($groupKey, $dataKey, $finder, $name, $expected)
    {
        $this->assertEquals($expected, $this->segDescription->$name($groupKey, $dataKey, $finder));
    }

    /** @test */
    public function it_throws_an_exception_if_the_key_for_the_given_method_does_not_exists()
    {
        $this->expectException(SegmentDesciptionException::class);
        $this->segDescription->name('groupKey', 'invaildMethodKey', 'invalidValueKeyOne');
    }

    /** @test */
    public function it_throws_an_exception_if_the_name_for_the_given_method_does_not_exists()
    {
        $this->expectException(SegmentDesciptionException::class);
        $this->segDescription->name('invalidGroupKey', 'invaildMethodKey', 'valueKeyOne');
    }

    /** @test */
    public function it_intanciate_only_one_differnt_object_per_json_file()
    {
        $sameSegDescription = SegmentDescription::make(__DIR__ . '/../Fixtures/Segments/meta/dummy.json');
        $differtSegDescription = SegmentDescription::make();

        $this->assertEquals($this->segDescription, $sameSegDescription);
        $this->assertNotEquals($this->segDescription, $differtSegDescription);
    }

    public function additionProvider()
    {
        return [
            ['groupKey', 'dataKey', 'valueKeyOne', 'name', 'NAME_ONE'],
            ['groupKey', 'dataKey', 'valueKeyOne', 'description', 'Description One'],
            ['groupKey', 'dataKey', 'valueKeyOne', 'tags', ['tag', 'tag_one']],
            [null, null, null, 'groupKeys', ['groupKey']],
            ['groupKey', null, null, 'dataKeys', ['dataKey']],
            ['groupKey', 'dataKey', null, 'dataKeys', ['dataKey']],
            ['groupKey', 'dataKey', null, 'valueKeys', ['valueKeyOne', 'valueKeyTwo']],
            ['groupKey', 'dataKey', ['tag'], 'taggedKeys', ['valueKeyOne', 'valueKeyTwo']],
            ['groupKey', 'dataKey', ['tag_one'], 'taggedKeys', ['valueKeyOne']],
            ['groupKey', 'dataKey', ['tag_one', 'tag_two'], 'taggedKeys', ['valueKeyOne', 'valueKeyTwo']],
        ];
    }
}
