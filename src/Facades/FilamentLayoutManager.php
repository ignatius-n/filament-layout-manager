<?php

namespace Asosick\ReorderWidgets\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Asosick\ReorderWidgets\FilamentLayoutManager
 */
class FilamentLayoutManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Asosick\ReorderWidgets\FilamentLayoutManager::class;
    }
}
