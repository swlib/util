<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/6/30 下午6:03
 */

namespace Swlib\Util;

use InvalidArgumentException;
use SplQueue;
use Swoole\Coroutine\Channel;

/**
 * Class MapPool
 * Use SplQueue(unlimited) or Channel(max_size)
 * @package Swlib\Util
 */
class MapPool
{
    use SingletonTrait;

    /** @var Channel[]|SplQueue[] */
    protected $resource_map = [];
    /** @var [][] */
    protected $status_map = [];

    public function init(string $key, int $max_size = -1): bool
    {
        if (!isset($this->resource_map[$key])) {
            if ($max_size < 0) {
                $this->resource_map[$key] = new SplQueue;
            } else {
                $this->resource_map[$key] = new Channel($max_size);
            }
            $this->status_map[$key] = [
                'max' => $max_size,
                'created' => 0,
                'in_pool' => 0,
                'reused' => 0,
                'destroyed' => 0
            ];
            return true;
        }
        return false;
    }

    public function create(array $options, string $key = null)
    {
        if (!$key) {
            throw new InvalidArgumentException('Argument#2 $key can not be empty!');
        }
        $this->status_map[$key]['created']++;
    }

    public function get(string $key)
    {
        if (!isset($this->resource_map[$key])) {
            $this->init($key);
        }
        $pool = $this->resource_map[$key];
        if ($pool instanceof SplQueue) {
            $available = $this->resource_map[$key]->count() > 0;
        } else {
            // the resource available or over the max num, use pop or yield and waiting
            $available = $this->resource_map[$key]->length() > 0 || $this->status_map[$key]['created'] >= $this->status_map[$key]['max'];
        }
        if ($available) {
            $this->status_map[$key]['reused']++;
            return $this->resource_map[$key]->pop(); //TODO: timeout
        } else {
            return null; // need create new one
        }
    }

    public function put($value, string $key = null)
    {
        if (!$key) {
            throw new InvalidArgumentException('Argument#2 $key can not be empty!');
        }
        $this->resource_map[$key]->push($value);
    }

    public function destroy($value, string $key = null)
    {
        if (!$key) {
            throw new InvalidArgumentException('Argument#2 $key can not be empty!');
        }
        $this->status_map[$key]['destroyed']++;
    }

    public function getStatus(string $key): array
    {
        if (!isset($this->status_map[$key])) {
            return [
                'max' => null,
                'created' => null,
                'in_pool' => null,
                'reused' => null,
                'destroyed' => null
            ];
        } else {
            $pool = $this->resource_map[$key];
            $in_pool = $pool instanceof SplQueue ? $pool->count() : $pool->length();
            $this->status_map[$key]['in_pool'] = $in_pool;
            return $this->status_map[$key];
        }
    }

    public function getAllStatus(bool $full = false): array
    {
        if ($full) {
            $ret = [];
            foreach ($this->status_map as $key => $value) {
                $ret[$key] = $this->getStatus($key);
            }
            return $ret;
        } else {
            return $this->status_map;
        }
    }

    public function getMax(string $key): ?int
    {
        return $this->status_map[$key]['max'] ?? null;
    }

    /**
     * expend will create the new chan
     *
     * @param string $key
     * @param int $max_size
     * @return int do what
     */
    public function setMax(string $key, int $max_size = -1): int
    {
        $is_exist = !$this->init($key, $max_size);
        if ($is_exist && $this->resource_map[$key] instanceof Channel) {
            $current_max = $this->status_map[$key]['max'];
            $this->status_map[$key]['max'] = $max_size;
            if ($max_size > $current_max || $max_size < 0) { // expend or unlimited
                if ($max_size < 0) { // chan to queue
                    $new_pool = new SplQueue;
                } else {
                    $new_pool = new Channel($max_size);
                }
                $old_chan = $this->resource_map[$key];
                while (!$old_chan->isEmpty()) {
                    $new_pool->push($old_chan->pop());
                }
                $old_chan->close();
                return 1; // expend
            } elseif ($max_size < $this->status_map[$key]['max']) {
                return -1; // reduce
            }
        }
        return 0; // do nothing
    }
}
