<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/5 下午8:24
 */

namespace Swlib\Util;

use Exception;

class Serialize
{
    public static function trace($traces): string
    {
        $default_trace = [
            'file' => 'unknown',
            'line' => 0,
            'function' => 'unknown'
        ];
        $r = '';
        foreach ($traces as $i => $t) {
            $t = $t + $default_trace;
            $r .= "#$i {$t['file']}({$t['line']}): ";
            if (isset($t['object']) and is_object($t['object'])) {
                $r .= get_class($t['object']) . '->';
            }
            $r .= "{$t['function']}()\n";
        }

        return $r;
    }

    public static function exception(Exception $exception): string
    {
        $file = $exception->getFile();
        $line = $exception->getLine();
        $code = $exception->getCode();
        $msg = $exception->getMessage();
        $trace = $exception->getTraceAsString();
        $r = "Exception: [{$code}] {$msg} in {$file} on line {$line}\n" .
            "Stack trace: " . $trace . "\n";

        return $r;
    }
}
