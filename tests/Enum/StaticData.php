<?php

namespace App\Tests\Enum;

use App\Tools\Constants;

class StaticData
{
    /******************************
     ********* CATEGORIES *********
     ******************************/
    public const CATEGORIES_ENDPOINTS_PREFIX = '/api/categories';

    /******************************
     *********** TODOS ************
     ******************************/
    public const TODOS_ENDPOINTS_PREFIX = '/api/todos';

    /**
     * Static functions
     */
    public static function getDefaultNumberOfCategoriesAndTodos(): int
    {
        return count(Constants::DEFAULT_CATEGORIES);
    }
}
