<?php
namespace steveLiuxu\JGService\Facades;

use Illuminate\Support\Facades\Facade;

class JGService extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'SteveLiuXu\JGService\JGService';
    }
}