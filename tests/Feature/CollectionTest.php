<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class CollectionTest extends TestCase
{
    public function testCollection()
    {
        $collection = collect([1, 2, 3]);

        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        foreach ($collection as $key => $value) {
            $this->assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1, 2, 3);

        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });

        $this->assertEqualsCanonicalizing([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["Dicky"]);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person("Dicky")], $result->all());
    }

    public function testMapSpred()
    {
        $collection = collect([
            ["Dicky", "Satria"],
            ["Meli", "Amelia"]
        ]);
        $result = $collection->mapSpread(function ($firstname, $lastname) {
            $fullname = $firstname . " " . $lastname;
            return new Person($fullname);
        });

        assertEquals([
            new Person("Dicky Satria"),
            new Person("Meli Amelia")
        ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                "name" => "Dicky Satria Putra Herlambang",
                "divisi" => "Back-End Developer"
            ],
            [
                "name" => "Fakhri Priwalraibana",
                "divisi" => "Back-End Developer"
            ],
            [
                "name" => "Farhan Julianto",
                "divisi" => "Front-End Developer"
            ]
        ]);
        $result = $collection->mapToGroups(function ($person) {
            return [
                $person["divisi"] => $person["name"]
            ];
        });

        $this->assertEquals([
            "Back-End Developer" => collect(["Dicky Satria Putra Herlambang", "Fakhri Priwalraibana"]),
            "Front-End Developer" => collect(["Farhan Julianto"])
        ], $result->all());
    }
}
