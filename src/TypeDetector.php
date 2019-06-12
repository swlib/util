<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/15 下午4:21
 */

namespace Swlib\Util;

use ArrayAccess;
use Iterator;

class TypeDetector
{
    public static function isIterable($var): bool
    {
        return is_array($var) || is_iterable($var) || (is_object($var) && $var instanceof Iterator);
    }

    public static function canBeString($var): bool
    {
        return is_string($var) || (is_object($var) && method_exists($var, '__toString'));
    }

    public static function canBeArray($var): bool
    {
        return is_array($var) || (is_object($var) && $var instanceof ArrayAccess);
    }

    public static function canBeCalled($var): bool
    {
        if (is_callable($var)) {
            return true;
        }
        if (is_array($var) && count($var) === 2) {
            return @method_exists(...$var);
        }
        if (is_object($var) && method_exists($var, '__invoke')) {
            return true;
        }
        return false;
    }
}
