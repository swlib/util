<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/6/18 上午3:10
 */

namespace Swlib\Util;

class Helper
{

    public static function call($callable, ...$arguments)
    {
        if (\is_object($callable) || (\is_string($callable) && \function_exists($callable))) {
            return $callable(...$arguments);
        } elseif (\is_array($callable)) {
            list($object, $method) = $callable;
            return \is_object($object) ? $object->$method(...$arguments) : $object::$method(...$arguments);
        } else {
            throw new \InvalidArgumentException('Call failed!');
        }
    }

}
