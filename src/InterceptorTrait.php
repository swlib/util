<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/3/24 上午1:02
 */

namespace Swlib\Util;

use InvalidArgumentException;

trait InterceptorTrait
{
    /**@var callable[][] */
    public $interceptors = [];

    protected static function filterInterceptor($interceptor): array
    {
        if (TypeDetector::canBeCalled($interceptor)) {
            return [$interceptor];
        } elseif (is_array($interceptor)) {
            foreach ($interceptor as $_interceptor) {
                if (!TypeDetector::canBeCalled($_interceptor)) {
                    goto _error;
                }
            }
            return $interceptor;
        } else {
            _error:
            throw new InvalidArgumentException('invalid interceptor');
        }
    }

    /**
     * Add an interceptor
     *
     * @param string $name
     * @param callable|callable[] $interceptor
     */
    public function withInterceptor(string $name, $interceptor)
    {
        $this->interceptors[$name] = static::filterInterceptor($interceptor);
    }

    /**
     * Add a function to the interceptor
     *
     * @param string $name
     * @param callable|callable[] $interceptor
     * @return self|$this
     */
    public function withAddedInterceptor(string $name, $interceptor): self
    {
        $this->interceptors[$name] = array_merge(
            $this->interceptors[$name] ?? [],
            static::filterInterceptor($interceptor)
        );

        return $this;
    }

    /**
     * Remove the interceptor
     *
     * @param string $name
     * @return self|$this
     */
    public function withoutInterceptor(string $name): self
    {
        unset($this->interceptors[$name]);

        return $this;
    }

    /**
     * Call the interceptor
     *
     * @param string $name
     * @param array ...$arguments
     * @return mixed
     */
    public function callInterceptor(string $name, ...$arguments)
    {
        if (!empty($this->interceptors[$name])) {
            foreach ($this->interceptors[$name] as $function) {
                $ret = Helper::call($function, ...$arguments);
                if ($ret !== null) {
                    return $ret;
                }
            }
        }

        return null;
    }
}
