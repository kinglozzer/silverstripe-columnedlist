<?php

class ColumnedList extends SS_ListDecorator {

	/**
	 * @param  int $columns
	 * @param  boolean $leftHeavy Whether the columns should be "left-heavy"
	 * @return array
	 */
	public function stack($columns, $leftHeavy = true) {
		$list = $this->list->toArray();
		$total = count($list);
		$partLength = floor($total / $columns);
		$partsRemaining = $total % $columns;
		$result = array();
		$offset = 0;
		
		for($i = 0; $i < $columns; $i++) {
			$handlingRemaining = ($leftHeavy) ? ($i < $partsRemaining) : ($i >= $partsRemaining - 1);
			$sliceLength = ($handlingRemaining) ? $partLength + 1 : $partLength;
			$column = array_slice($list, $offset, $sliceLength);
			$result[$i] = new ArrayList($column);
			$offset += $sliceLength;
		}

		return $result;
	}

	/**
	 * Stacks data in columns
	 * 
	 * @param  int $columns The target number of columns
	 * @param  string $children Name of the control under which children can be iterated on
	 * @param  boolean $leftHeavy Whether the columns should be "left-heavy"
	 * @return ArrayList
	 */
	public function Stacked($columns, $children = 'Children', $leftHeavy = true) {
		$stacked = $this->stack($columns, $leftHeavy);
		$result  = new ArrayList();

		foreach($stacked as $list) {
			$firstlast = '';
			if($list == $stacked->First()) {
				$firstlast = 'first';
			} else if($list == $stacked->Last()) {
				$firstlast = 'last';
			}

			$list = self::create($list);
			$result->push(new ArrayData(array(
				$children => $list,
				'FirstLast' => $firstlast
			)));
		}

		return $result;
	}

}
