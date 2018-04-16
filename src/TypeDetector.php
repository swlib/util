<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/15 下午4:21
 */

namespace Swlib\Util;

class TypeDetector
{

    public static function canBeArray($var): bool
    {
        return is_array($var) || (is_object($var) && $var instanceof \ArrayAccess);
    }

    public static function canBeString($var): bool
    {
        return is_string($var) || (is_object($var) && method_exists($var, '__toString'));
    }

    public static function canBeCalled($var): bool
    {
        return is_callable($var) || (is_object($var) && method_exists($var, '__invoke'));
    }

    public static function isIterable($var): bool
    {
        return is_array($var) || (is_object($var) && $var instanceof \Iterator);
    }

}