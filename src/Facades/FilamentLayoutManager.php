<?php

namespace Asosick\FilamentLayoutManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Asosick\FilamentLayoutManager\FilamentLayoutManager
 */
class FilamentLayoutManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Asosick\FilamentLayoutManager\FilamentLayoutManagerPlugin::class;
    }
}
