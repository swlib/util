<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/5 上午2:50
 */

namespace Swlib\Util;

use ArrayObject;

class ArrayMap extends ArrayObject
{
    function get(string $path)
    {
        $path = explode(".", $path);
        $data = null;
        while ($key = array_shift($path)) {
            if (isset($this[$key])) {
                $data = $this[$key];
            } else {
                return null;
            }
        }

        return $data;
    }

    /**
     * @param string $path
     * @param $value
     * @return $this
     */
    function set(string $path, $value): self
    {
        $path = explode(".", $path);
        $temp = $this;
        while ($key = array_shift($path)) {
            $temp = &$temp[$key];
        }
        if ($key) {
            $temp = $value;
        }

        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function delete(string $key): self
    {
        $path = explode(".", $key);
        $last_key = array_pop($path);
        $temp = [];
        while ($key = array_shift($path)) {
            if (isset($this[$key])) {
                $temp = &$this[$key];
            } else {
                return $this;
            }
        }
        if (isset($temp[$last_key])) {
            unset($temp[$last_key]);
        }

        return $this;
    }

    /** @return $this */
    public function unique(): self
    {
        return new ArrayMap(array_unique($this->getArrayCopy()));
    }

    /**
     * Get duplicate values in an array
     * @return $this
     */
    public function multiple(): self
    {
        $unique_array = array_unique($this->getArrayCopy());
        return new ArrayMap(array_diff_assoc($this->getArrayCopy(), $unique_array));
    }

    /** @return $this */
    public function asort(): self
    {
        parent::asort();
        return $this;
    }

    /** @return $this */
    public function ksort(): self
    {
        parent::ksort();
        return $this;
    }

    /**
     * sort
     * @param int $sort_flags
     * @return $this
     */
    public function sort($sort_flags = SORT_REGULAR): self
    {
        $temp = $this->getArrayCopy();
        sort($temp, $sort_flags);
        return new ArrayMap($temp);
    }

    /**
     * @param $column
     * @param null $index_key
     * @return $this
     */
    public function column($column, $index_key = null): self
    {
        return new ArrayMap(array_column($this->getArrayCopy(), $column, $index_key));
    }

    /** @return $this */
    public function flip(): self
    {
        return new ArrayMap(array_flip($this->getArrayCopy()));
    }

    /**
     * filter this array
     * @param string|array $keys Needed/Excluded keys
     * @param bool $exclude Exclude or Include
     * @return $this
     */
    public function filter($keys, $exclude = false): self
    {
        if (is_string($keys)) {
            $keys = explode(',', $keys);
        }
        $new = [];
        foreach ($this->getArrayCopy() as $name => $value) {
            if (!$exclude) {
                in_array($name, $keys) ? $new[$name] = $value : null;
            } else {
                in_array($name, $keys) ? null : $new[$name] = $value;
            }
        }
        return new ArrayMap($new);
    }

    /** @return $this */
    public function keys(): self
    {
        return new ArrayMap(array_keys($this->getArrayCopy()));
    }

    /** @return $this */
    public function values(): self
    {
        return new ArrayMap(array_values($this->getArrayCopy()));
    }

    /** @return $this */
    public function reset(): self
    {
        foreach ($this as $key => $item) {
            unset($this[$key]);
        }
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function load(array $data): self
    {
        parent::__construct($data);

        return $this;
    }

    function getArrayCopy(): array
    {
        return (array)$this;
    }

    function __get($name)
    {
        if (isset($this[$name])) {
            return $this[$name];
        } else {
            return null;
        }
    }

    function __set($name, $value): void
    {
        $this[$name] = $value;
    }

    function __toString(): string
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);
    }
}
