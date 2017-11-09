<?php

namespace Kinglozzer\SilverStripeColumnedList;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\ListDecorator;
use SilverStripe\View\ArrayData;

class ColumnedList extends ListDecorator
{
    /**
     * @param int $columns
     * @param boolean $leftHeavy Whether the columns should be "left-heavy"
     * @return array
     */
    public function stack($columns, $leftHeavy = true)
    {
        $list = $this->getList()->toArray();
        $total = count($list);
        $partLength = floor($total / $columns);
        $remainder = $total % $columns;
        $result = [];
        $offset = 0;

        // Loop over the number of columns
        for ($i = 0; $i < $columns; $i++) {
            $handlingRemainder = ($leftHeavy) ? ($i < $remainder) : ($i >= $columns - $remainder);

            $sliceLength = ($handlingRemainder) ? $partLength + 1 : $partLength;
            $column = array_slice($list, $offset, $sliceLength);
            $offset += $sliceLength;

            $result[$i] = new ArrayList($column);
        }

        return $result;
    }

    /**
     * Stacks data in columns
     *
     * @param int $columns The target number of columns
     * @param string $children Name of the control under which children can be iterated on
     * @param boolean $leftHeavy Whether the columns should be "left-heavy"
     * @return ArrayList
     */
    public function Stacked($columns, $children = 'Children', $leftHeavy = true)
    {
        $stacked = $this->stack($columns, $leftHeavy);
        $result = new ArrayList();

        foreach ($stacked as $list) {
            $list = Injector::inst()->createWithArgs(static::class, [$list]);
            $result->push(new ArrayData([$children => $list]));
        }

        return $result;
    }
}
