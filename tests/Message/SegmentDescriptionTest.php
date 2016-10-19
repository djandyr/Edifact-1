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
    public function it_returns_the_meta_attributes($method, $finder, $name, $expected)
    {
        $this->assertEquals($expected, $this->segDescription->$name($method, $finder));
    }

    /** @test */
    public function it_throws_an_exception_if_the_name_for_the_given_method_does_not_exists()
    {
        $this->expectException(SegmentDesciptionException::class);
        $this->segDescription->name('invaild_method', 'dummyKey');
    }

    /** @test */
    public function it_throws_an_exception_if_the_key_for_the_given_method_does_not_exists()
    {
        $this->expectException(SegmentDesciptionException::class);
        $this->segDescription->name('dummyMethod', 'invalidKey');
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
            ['dummyMethod', 'dummyKey', 'name', 'DUMMY_NAME'],
            ['dummyMethod', 'dummyKey', 'description', 'Dummy description'],
            ['dummyMethod', 'dummyKey', 'tags', ['dummy_tag', 'dummy_tag_one']],
            ['dummyMethod', null, 'keys', ['dummyKey', 'dummyKeyTwo']],
            ['dummyMethod', ['dummy_tag'], 'taggedKeys', ['dummyKey', 'dummyKeyTwo']],
            ['dummyMethod', ['dummy_tag_one'], 'taggedKeys', ['dummyKey']],
            ['dummyMethod', ['dummy_tag_one', 'dummy_tag_two'], 'taggedKeys', ['dummyKey', 'dummyKeyTwo']],
        ];
    }
}
