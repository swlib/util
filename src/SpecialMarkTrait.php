<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/12 下午9:48
 */

namespace Swlib\Util;

trait SpecialMarkTrait
{
    /** @var $special_marks array "mark/remark" */
    public $special_marks = ['default' => null];

    /**
     * Get special mark of this object
     *
     * @param string $name
     * @return mixed
     */
    public function getSpecialMark(string $name = 'default')
    {
        return $this->special_marks[$name] ?? null;
    }

    /**
     * Mark this object
     *
     * @param mixed $mark
     * @return $this
     */
    public function withSpecialMark($mark, string $name = 'default'): self
    {
        $this->special_marks[$name] = $mark;

        return $this;
    }
}
