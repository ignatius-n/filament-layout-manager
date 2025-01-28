<?php

namespace Asosick\ReorderWidgets\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Asosick\ReorderWidgets\ReorderWidgets
 */
class ReorderWidgets extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Asosick\ReorderWidgets\ReorderWidgets::class;
    }
}
