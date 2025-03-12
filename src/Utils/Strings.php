<?php

namespace App\Utils;

class Strings
{
    /**
     * @param int $length
     * @return string
     */
    public static function hash(int $length = 32): string
    {
        return substr(
            str_shuffle(md5(microtime()) . strtoupper(md5(microtime()))),
            0,
            $length,
        );
    }
}