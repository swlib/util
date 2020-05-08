<?php
/**
 * Author: Twosee <twose@qq.com>
 * Date: 2018/6/29 下午11:29
 */

namespace Swlib\Util;

trait SingletonTrait
{
    private static $instance;

    static function getInstance(...$args)
    {
        if (!isset(self::$instance)) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
}
