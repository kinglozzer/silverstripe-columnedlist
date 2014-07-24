<?php

class ColumnedListTest extends SapphireTest {

	/**
	 * Check breaking into columns with no remainder works as expected:
	 * | 1 | 3 |
	 * | 2 | 4 |
	 * 
	 * @return void
	 */
	public function testStackWithNoRemainder() {
		$data = ArrayList::create(array(
			array('ID' => 1), array('ID' => 2), array('ID' => 3), array('ID' => 4)
		));

		$columnedList = ColumnedList::create($data);
		$stacked = $columnedList->stack(2);
		
		$this->assertEquals(2, count($stacked));
		$this->assertEquals(array('ID' => 1), $stacked[0]->offsetGet(0));
		$this->assertEquals(array('ID' => 2), $stacked[0]->offsetGet(1));
		$this->assertEquals(array('ID' => 3), $stacked[1]->offsetGet(0));
		$this->assertEquals(array('ID' => 4), $stacked[1]->offsetGet(1));
	}

	/**
	 * Check breaking into columns with less items than columns works as expected:
	 * | 1 | 2 | _ |
	 * 
	 * @return void
	 */
	public function testStackWithFewItems() {
		$data = ArrayList::create(array(
			array('ID' => 1), array('ID' => 2)
		));

		$columnedList = ColumnedList::create($data);
		$stacked = $columnedList->stack(3);
		
		$this->assertEquals(3, count($stacked));
		$this->assertEquals(array('ID' => 1), $stacked[0]->offsetGet(0));
		$this->assertEquals(array('ID' => 2), $stacked[1]->offsetGet(0));
		$this->assertNull($stacked[2]->offsetGet(0));
	}

	/**
	 * Check breaking into columns with remainder works as expected:
	 * | 1 | 3 | 5 |
	 * | 2 | 4 | _ |
	 * 
	 * @return void
	 */
	public function testStackLeftHeavyWithRemainder() {
		$data = ArrayList::create(array(
			array('ID' => 1), array('ID' => 2), array('ID' => 3), array('ID' => 4), array('ID' => 5)
		));

		$columnedList = ColumnedList::create($data);
		$stacked = $columnedList->stack(3);
		
		$this->assertEquals(3, count($stacked));
		$this->assertEquals(array('ID' => 1), $stacked[0]->offsetGet(0));
		$this->assertEquals(array('ID' => 2), $stacked[0]->offsetGet(1));
		$this->assertEquals(array('ID' => 3), $stacked[1]->offsetGet(0));
		$this->assertEquals(array('ID' => 4), $stacked[1]->offsetGet(1));
		$this->assertEquals(array('ID' => 5), $stacked[2]->offsetGet(0));
		$this->assertNull($stacked[2]->offsetGet(1));
	}

	/**
	 * Check breaking into "right-heavy" columns with remainder works as expected:
	 * | 1 | 2 | 4 |
	 * | _ | 3 | 5 |
	 * 
	 * @return void
	 */
	public function testStackRightHeavyWithRemainder() {
		$data = ArrayList::create(array(
			array('ID' => 1), array('ID' => 2), array('ID' => 3), array('ID' => 4), array('ID' => 5)
		));

		$columnedList = ColumnedList::create($data);
		$stacked = $columnedList->stack(3, false);
		
		$this->assertEquals(3, count($stacked));
		$this->assertEquals(array('ID' => 1), $stacked[0]->offsetGet(0));
		$this->assertNull($stacked[0]->offsetGet(1));
		$this->assertEquals(array('ID' => 2), $stacked[1]->offsetGet(0));
		$this->assertEquals(array('ID' => 3), $stacked[1]->offsetGet(1));
		$this->assertEquals(array('ID' => 4), $stacked[2]->offsetGet(0));
		$this->assertEquals(array('ID' => 5), $stacked[2]->offsetGet(1));
	}

	/**
	 * Test the template method returns expected data.
	 * 
	 * @return void
	 */
	public function testStacked() {
		$data = ArrayList::create(array(
			array('ID' => 1), array('ID' => 2), array('ID' => 3), array('ID' => 4), array('ID' => 5)
		));

		$columnedList = ColumnedList::create($data);
		
		/*
			Test typical stacked list:
			| 1 | 3 | 5 |
	 		| 2 | 4 | _ |
	 	*/
		$stacked = $columnedList->Stacked(3);
		$this->assertEquals(3, $stacked->count());
		$this->assertInstanceOf('ArrayData', $stacked->first());
		$this->assertEquals(2, $stacked->first()->Children->count());
		$this->assertEquals(array('ID' => 1), $stacked->first()->Children->first());
		$this->assertNull($stacked->last()->Children->offsetGet(1));

		/*
			Test custom accessor key
		*/
		$stacked = $columnedList->Stacked(3, 'TestChildren');
		$this->assertInstanceOf('ColumnedList', $stacked->first()->TestChildren);

		/*
			Test "right-heavy" stacked list:
			| 1 | 2 | 4 |
			| _ | 3 | 5 |
	 	*/
		$stacked = $columnedList->Stacked(3, 'Children', 0);
		$this->assertEquals(3, $stacked->count());
		$this->assertInstanceOf('ArrayData', $stacked->first());
		$this->assertEquals(1, $stacked->first()->Children->count());
		$this->assertEquals(2, $stacked->last()->Children->count());
		$this->assertEquals(array('ID' => 5), $stacked->last()->Children->last());
	}

}