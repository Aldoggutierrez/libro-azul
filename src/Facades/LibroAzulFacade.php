<?php

namespace Abiside\LibroAzul\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class LibroAzulFacade
 * @package Abiside\LibroAzul
 */
class LibroAzulFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'libroazul';
    }
}