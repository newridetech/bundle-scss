<?php

namespace Newride\Scss\Facades;

use Illuminate\Support\Facades\Facade;

class Scss extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'scss';
    }
}
