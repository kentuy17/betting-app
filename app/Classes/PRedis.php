<?php

namespace App\Classes;

use Illuminate\Support\Facades\Redis;


class PRedis
{
    private $itemKey;

    public function current()
    {
        return Redis::get($this->itemKey);
    }

    public static function incr($key, $incr)
    {
        $tmp = Redis::get($key);
        return Redis::set($key, $tmp + $incr);
    }
}
