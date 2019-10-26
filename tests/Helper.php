<?php

namespace NetRandom\Tests;

class Helper
{
    public static function FloatToBinStr(float $value) : string
    {
        $bin = '';
        $packed = pack('d', $value); // use 'f' for 32 bit
        foreach(str_split(strrev($packed)) as $char)
            $bin .= str_pad(decbin(ord($char)), 8, 0, STR_PAD_LEFT);
        return $bin;
    }
}
