<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/14 下午11:18
 */

namespace Swlib\Util;

trait DataParserTrait
{

    public $dataIsWaitingToBeParsed;

    public $dataHasParsed = [];

    private function __dataParserInitialization(&$data)
    {
        if (isset($data)) {
            if (!is_string($data) && !(is_object($data) && method_exists($data, '__toString'))) {
                throw new \InvalidArgumentException('Bind data must be string type or has toString method.');
            }
        }
        $this->dataIsWaitingToBeParsed = &$data;
    }

    public function getParsedJson(bool $reParse = false): array
    {
        if (isset($this->dataHasParsed['json']) && !$reParse) {
            return $this->dataHasParsed['json'];
        } else {
            $ret = json_decode($this->dataIsWaitingToBeParsed, true);
            if ($ret !== false) {
                $this->dataHasParsed['json'] = $ret;
            } else {
                $ret = [];
            }
        }

        return $ret;
    }

    public function getParsedJsonObject(bool $reParse = false): object
    {
        if (isset($this->dataHasParsed['jsonObject']) && !$reParse) {
            return $this->dataHasParsed['jsonObject'];
        } else {
            $ret = json_decode($this->dataIsWaitingToBeParsed);
            if ($ret !== false) {
                $this->dataHasParsed['jsonObject'] = $ret;
            } else {
                $ret = (object)[];
            }
        }

        return $ret;
    }

    public function getParsedQuery(bool $reParse = false): array
    {
        if (isset($this->dataHasParsed['query']) && !$reParse) {
            $ret = $this->dataHasParsed['query'];
        } else {
            parse_str($this->dataIsWaitingToBeParsed, $ret);
            $this->dataHasParsed['query'] = $ret;
        }

        return $ret;
    }

    public function getParsedXml(bool $reParse = false): \SimpleXMLElement
    {
        if (isset($this->dataHasParsed['xml']) && !$reParse) {
            return $this->dataHasParsed['xml'];
        } else {
            return $this->dataHasParsed['xml'] =
                new \SimpleXMLElement((string)$this->dataIsWaitingToBeParsed);
        }
    }

    public function getParsedHtml(bool $reParse = false): \DOMDocument
    {
        if (isset($this->dataHasParsed['html']) && !$reParse) {
            return $this->dataHasParsed['html'];
        } else {
            $ret = new \DOMDocument($this->dataIsWaitingToBeParsed);
            $ret->loadHTML((string)$this->dataIsWaitingToBeParsed);
            return $this->dataHasParsed['html'] = $ret;
        }
    }

}