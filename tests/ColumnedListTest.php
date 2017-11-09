<?php

namespace Kinglozzer\SilverStripeColumnedList\Tests;

use Kinglozzer\SilverStripeColumnedList\ColumnedList;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;

class ColumnedListTest extends SapphireTest
{
    /**
     * Check breaking into columns with no remainder works as expected:
     * | 1 | 3 |
     * | 2 | 4 |
     *
     * @return void
     */
    public function testStackWithNoRemainder()
    {
        $data = ArrayList::create([
            ['ID' => 1], ['ID' => 2], ['ID' => 3],['ID' => 4]
        ]);

        $columnedList = ColumnedList::create($data);
        $stacked = $columnedList->stack(2);

        $this->assertEquals(2, count($stacked));
        $this->assertEquals(['ID' => 1], $stacked[0]->offsetGet(0));
        $this->assertEquals(['ID' => 2], $stacked[0]->offsetGet(1));
        $this->assertEquals(['ID' => 3], $stacked[1]->offsetGet(0));
        $this->assertEquals(['ID' => 4], $stacked[1]->offsetGet(1));
    }

    /**
     * Check breaking into columns with less items than columns works as expected:
     * | 1 | 2 | _ |
     *
     * @return void
     */
    public function testStackWithFewItems()
    {
        $data = ArrayList::create([
            ['ID' => 1], ['ID' => 2]
        ]);

        $columnedList = ColumnedList::create($data);
        $stacked = $columnedList->stack(3);

        $this->assertEquals(3, count($stacked));
        $this->assertEquals(['ID' => 1], $stacked[0]->offsetGet(0));
        $this->assertEquals(['ID' => 2], $stacked[1]->offsetGet(0));
        $this->assertNull($stacked[2]->offsetGet(0));
    }

    /**
     * Check breaking into columns with remainder works as expected:
     * | 1 | 3 | 5 |
     * | 2 | 4 | _ |
     *
     * @return void
     */
    public function testStackLeftHeavyWithRemainder()
    {
        $data = ArrayList::create([
            ['ID' => 1], ['ID' => 2], ['ID' => 3], ['ID' => 4], ['ID' => 5]
        ]);

        $columnedList = ColumnedList::create($data);
        $stacked = $columnedList->stack(3);

        $this->assertEquals(3, count($stacked));
        $this->assertEquals(['ID' => 1], $stacked[0]->offsetGet(0));
        $this->assertEquals(['ID' => 2], $stacked[0]->offsetGet(1));
        $this->assertEquals(['ID' => 3], $stacked[1]->offsetGet(0));
        $this->assertEquals(['ID' => 4], $stacked[1]->offsetGet(1));
        $this->assertEquals(['ID' => 5], $stacked[2]->offsetGet(0));
        $this->assertNull($stacked[2]->offsetGet(1));
    }

    /**
     * Check breaking into "right-heavy" columns with remainder works as expected:
     * | 1 | 2 | 4 |
     * | _ | 3 | 5 |
     *
     * @return void
     */
    public function testStackRightHeavyWithRemainder()
    {
        $data = ArrayList::create([
            ['ID' => 1], ['ID' => 2], ['ID' => 3], ['ID' => 4], ['ID' => 5]
        ]);

        $columnedList = ColumnedList::create($data);
        $stacked = $columnedList->stack(3, false);

        $this->assertEquals(3, count($stacked));
        $this->assertEquals(['ID' => 1], $stacked[0]->offsetGet(0));
        $this->assertNull($stacked[0]->offsetGet(1));
        $this->assertEquals(['ID' => 2], $stacked[1]->offsetGet(0));
        $this->assertEquals(['ID' => 3], $stacked[1]->offsetGet(1));
        $this->assertEquals(['ID' => 4], $stacked[2]->offsetGet(0));
        $this->assertEquals(['ID' => 5], $stacked[2]->offsetGet(1));
    }

    /**
     * Check breaking into "right-heavy" columns with remainder works as expected:
     * | 1 | 2 | 3 | 5 |
     * | _ | _ | 4 | 6 |
     *
     * @return void
     */
    public function testStackRightHeavyWithRemainderEven()
    {
        $data = ArrayList::create([
            ['ID' => 1], ['ID' => 2], ['ID' => 3], ['ID' => 4], ['ID' => 5], ['ID' => 6]
        ]);

        $columnedList = ColumnedList::create($data);
        $stacked = $columnedList->stack(4, false);

        $this->assertEquals(4, count($stacked));
        $this->assertEquals(['ID' => 1], $stacked[0]->offsetGet(0));
        $this->assertNull($stacked[0]->offsetGet(1));
        $this->assertEquals(['ID' => 2], $stacked[1]->offsetGet(0));
        $this->assertNull($stacked[1]->offsetGet(1));
        $this->assertEquals(['ID' => 3], $stacked[2]->offsetGet(0));
        $this->assertEquals(['ID' => 4], $stacked[2]->offsetGet(1));
        $this->assertEquals(['ID' => 5], $stacked[3]->offsetGet(0));
        $this->assertEquals(['ID' => 6], $stacked[3]->offsetGet(1));
    }

    /**
     * Test the template method returns expected data.
     *
     * @return void
     */
    public function testStacked()
    {
        $data = ArrayList::create([
            ['ID' => 1], ['ID' => 2], ['ID' => 3], ['ID' => 4], ['ID' => 5]
        ]);

        $columnedList = ColumnedList::create($data);

        /*
            Test typical stacked list:
            | 1 | 3 | 5 |
             | 2 | 4 | _ |
         */
        $stacked = $columnedList->Stacked(3);
        $this->assertEquals(3, $stacked->count());
        $this->assertInstanceOf(ArrayData::class, $stacked->first());
        $this->assertEquals(2, $stacked->first()->Children->count());
        $this->assertEquals(['ID' => 1], $stacked->first()->Children->first());
        $this->assertNull($stacked->last()->Children->offsetGet(1));

        /*
            Test custom accessor key
        */
        $stacked = $columnedList->Stacked(3, 'TestChildren');
        $this->assertInstanceOf(ColumnedList::class, $stacked->first()->TestChildren);

        /*
            Test "right-heavy" stacked list:
            | 1 | 2 | 4 |
            | _ | 3 | 5 |
         */
        $stacked = $columnedList->Stacked(3, 'Children', 0);
        $this->assertEquals(3, $stacked->count());
        $this->assertInstanceOf(ArrayData::class, $stacked->first());
        $this->assertEquals(1, $stacked->first()->Children->count());
        $this->assertEquals(2, $stacked->last()->Children->count());
        $this->assertEquals(['ID' => 5], $stacked->last()->Children->last());
    }
}
