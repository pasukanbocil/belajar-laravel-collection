<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\LazyCollection;
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

    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6])
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(["name", "country"]);
        $collection2 = collect(["Dicky Satria Ph", "Indonesia"]);
        $collection3 = $collection1->combine($collection2);


        $this->assertEqualsCanonicalizing([
            "name" => "Dicky Satria Ph",
            "country" => "Indonesia"
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);
        $result = $collection->collapse();

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "Dicky",
                "hobbies" => ["Coding", "Gaming"]
            ],
            [
                "name" => "Eko",
                "hobbies" => ["Writing", "Reading"]
            ]
        ]);
        $result = $collection->flatMap(function ($item) {
            $hobbies = $item["hobbies"];
            return $hobbies;
        });
        $this->assertEqualsCanonicalizing(["Coding", "Gaming", "Writing", "Reading"], $result->all());
    }

    public function testStringRepresentation()
    {
        $collection = collect(["Dicky", "Satria", "Khannedy"]);

        $this->assertEquals("Dicky-Satria-Khannedy", $collection->join("-"));
        $this->assertEquals("Dicky-Satria_Khannedy", $collection->join("-", "_"));
        $this->assertEquals("Dicky, Satria And Khannedy", $collection->join(", ", " And "));
    }

    public function testFilter()
    {
        $collection = collect([
            "Dicky" => 100,
            "Kude" => 80,
            "Arya" => 90
        ]);
        $result = $collection->filter(function ($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            "Dicky" => 100,
            "Arya" => 90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $result = $collection->filter(function ($value, $key) {
            return $value % 2 == 0;
        });

        $this->assertEqualsCanonicalizing([2, 4, 6, 8, 10], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            "Dicky" => 100,
            "Kude" => 80,
            "Arya" => 90
        ]);
        [$result1, $result2] = $collection->partition(function ($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            "Dicky" => 100,
            "Arya" => 90
        ], $result1->all());
        $this->assertEquals([

            "Kude" => 80
        ], $result2->all());
    }


    public function testTesting()
    {
        $collection = collect(["Dicky", "Satria", "Putra"]);
        $this->assertTrue($collection->contains("Dicky"));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == "Putra";
        }));
    }

    public function testGroupBy()
    {
        $collection = collect([
            [
                "name" => "Dicky Satria Putra Herlambang",
                "divisi" => "Backend Developer"
            ],
            [
                "name" => "Fakhri Priwalraibana",
                "divisi" => "Backend Developer"
            ],
            [
                "name" => "Farhan Julianto",
                "divisi" => "Frontend Developer"
            ],
            [
                "name" => "Khoirunnisa Nurul",
                "divisi" => "Frontend Developer"
            ]
        ]);

        $result = $collection->groupBy("divisi");

        assertEquals([
            "Backend Developer" => collect(
                [
                    [
                        "name" => "Dicky Satria Putra Herlambang",
                        "divisi" => "Backend Developer"
                    ],


                    [
                        "name" => "Fakhri Priwalraibana",
                        "divisi" => "Backend Developer"
                    ]
                ]
            ),
            "Frontend Developer" => collect(
                [
                    [
                        "name" => "Farhan Julianto",
                        "divisi" => "Frontend Developer"
                    ],

                    [
                        "name" => "Khoirunnisa Nurul",
                        "divisi" => "Frontend Developer"
                    ]
                ]
            )
        ], $result->all());


        $result = $collection->groupBy(function ($value, $key) {
            return strtolower($value["divisi"]);
        });
        assertEquals([
            "backend developer" => collect(
                [
                    [
                        "name" => "Dicky Satria Putra Herlambang",
                        "divisi" => "Backend Developer"
                    ],


                    [
                        "name" => "Fakhri Priwalraibana",
                        "divisi" => "Backend Developer"
                    ]
                ]
            ),
            "frontend developer" => collect(
                [
                    [
                        "name" => "Farhan Julianto",
                        "divisi" => "Frontend Developer"
                    ],

                    [
                        "name" => "Khoirunnisa Nurul",
                        "divisi" => "Frontend Developer"
                    ]
                ]
            )
        ], $result->all());
    }

    public function testSlicing()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $result = $collection->slice(4);

        $this->assertEqualsCanonicalizing([5, 6, 7, 8, 9, 10], $result->all());

        $result = $collection->slice(3, 2);
        $this->assertEqualsCanonicalizing([4, 5], $result->all());
    }

    public function testTake()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->take(3);
        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all());

        $result = $collection->takeUntil(function ($value, $key) {
            return $value == 3;
        });
        $this->assertEqualsCanonicalizing([1, 2], $result->all());

        $result = $collection->takeWhile(function ($value, $key) {
            return $value < 3;
        });
        $this->assertEqualsCanonicalizing([1, 2], $result->all());
    }

    public function testSkip()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result = $collection->skip(3);
        $this->assertEqualsCanonicalizing([4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->skipUntil(function ($value, $key) {
            return $value == 3;
        });
        $this->assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->skipWhile(function ($value, $key) {
            return $value < 3;
        });
        $this->assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testChunk()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $result = $collection->chunk(3);

        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all()[0]->all());
        $this->assertEqualsCanonicalizing([4, 5, 6], $result->all()[1]->all());
        $this->assertEqualsCanonicalizing([7, 8, 9], $result->all()[2]->all());
        $this->assertEqualsCanonicalizing([10], $result->all()[3]->all());
    }

    public function testFirst()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result  = $collection->first();

        $this->assertEquals(1, $result);

        $result = $collection->first(function ($value, $key) {
            return $value > 5;
        });
        $this->assertEquals(6, $result);
    }

    public function testLast()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result  = $collection->last();

        $this->assertEquals(9, $result);

        $result = $collection->last(function ($value, $key) {
            return $value < 5;
        });
        $this->assertEquals(4, $result);
    }

    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->random();

        $this->assertTrue(in_array($result, [1, 2, 3, 4, 5, 6, 7, 8, 9]));

        // $result = $collection->random(5);
        // $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5], $result->all());
    }

    public function testCheckingExisting()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $this->assertTrue($collection->isNotEmpty());
        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($collection->contains(1));
        $this->assertFalse($collection->contains(10));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == 8;
        }));
    }

    public function testOrdering()
    {
        $collection = collect([1, 3, 2, 4, 6, 5, 7, 9, 8]);
        $result = $collection->sort();
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());

        $result = $collection->sortDesc();
        $this->assertEqualsCanonicalizing([9, 8, 7, 6, 5, 4, 3, 2, 1], $result->all());
    }

    public function testAggregate()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->sum();
        $this->assertEquals(45, $result);

        $result = $collection->avg();
        $this->assertEquals(5, $result);

        $result = $collection->min();
        $this->assertEquals(1, $result);

        $result = $collection->max();
        $this->assertEquals(9, $result);
    }

    public function testReduce()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result = $collection->reduce(function ($carry, $item) {
            return $carry + $item;
        });
        $this->assertEquals(45, $result);
    }

    public function testLazzyCollection()
    {
        $collection = LazyCollection::make(function () {
            $value = 0;

            while (true) {
                yield $value;
                $value++;
            }
        });
        $result = $collection->take(10);
        $this->assertEqualsCanonicalizing([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }
}
