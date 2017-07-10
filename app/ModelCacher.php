<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;

trait ModelCacher {
    /**
     * Collection to cache to prevent n + 1 queries.
     *
     * @var Collection
     */
    protected static $cache;

    /**
     * @var string
     */
    protected static $columns;

    /**
     * Cache all categories and return it. This is to prevent
     * redundent queries during mass processes.
     *
     * @param array $columns
     * @return Collection|static[]
     */
    public static function cache($columns = ['*'])
    {
        $cols = implode(' ', $columns);

        if (static::$cache && static::$columns == $cols) {
            return static::$cache;
        }

        static::$columns = $cols;
        static::$cache = static::all($columns);

        return static::$cache;
    }
}
