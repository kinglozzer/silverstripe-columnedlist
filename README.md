# ColumnedList

[![Build Status](https://api.travis-ci.org/kinglozzer/silverstripe-columnedlist.svg?branch=master)](https://travis-ci.org/kinglozzer/silverstripe-columnedlist) [![Latest Stable Version](https://poser.pugx.org/kinglozzer/silverstripe-columnedlist/version.svg)](https://packagist.org/packages/kinglozzer/silverstripe-columnedlist) [![Total Downloads](https://poser.pugx.org/kinglozzer/silverstripe-columnedlist/downloads.svg)](https://packagist.org/packages/kinglozzer/silverstripe-columnedlist) [![License](https://poser.pugx.org/kinglozzer/silverstripe-columnedlist/license.svg)](https://packagist.org/packages/kinglozzer/silverstripe-columnedlist)

An `SS_ListDecorator` to facilitate stacking data vertically in columns. Supports left and right “weighting”.

## Example:

```php
use Kinglozzer\SilverStripeColumnedList\ColumnedList;
use SilverStripe\CMS\Model\SiteTree;

class Page extends SiteTree
{
    public function ColumnData()
    {
        return ColumnedList::create($this->SomeDataList());
    }
}
```

```php
<% loop ColumnData.Stacked(3) %>
    <div style="float: left">
        <h3>Column {$Pos}</h3>
        <ul>
            <% loop Children %>
                <li>Item {$Pos}</li>
            <% end_loop %>
        </ul>
    </div>
<% end_loop %>
```

Assuming `SomeDataList()` contains 5 items, the output would be:

Column 1 | Column 2 | Column 3
--- | --- | ---
Item 1 | Item 3 | Item 5
Item 2 | Item 4 | .

## “Right-heavy” stacking:

Using the same above example:

```php
<% loop ColumnData.Stacked(3, 'Children', 0) %>
    <div style="float: left">
        <h3>Column {$Pos}</h3>
        <ul>
            <% loop Children %>
                <li>Item {$Pos}</li>
            <% end_loop %>
        </ul>
    </div>
<% end_loop %>
```

Assuming `SomeDataList()` contains 5 items, the output would be:

Column 1 | Column 2 | Column 3
--- | --- | ---
Item 1 | Item 2 | Item 4
. | Item 3 | Item 5
