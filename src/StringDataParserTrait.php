<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/14 下午11:18
 */

namespace Swlib\Util;

trait StringDataParserTrait
{

    private $stringDataIsWaitingToBeParsed;
    private $stringDataHasParsed = [];

    private function __stringDataParserInitialization(&$data)
    {
        if (isset($data)) {
            if (!is_string($data) && !(is_object($data) && method_exists($data, '__toString'))) {
                throw new \InvalidArgumentException('Bind data must be string type or has toString method.');
            }
        }
        if (is_object($data)) {
            $this->stringDataIsWaitingToBeParsed = $data;
        } else {
            $this->stringDataIsWaitingToBeParsed = &$data;
        }
    }

    public function getParsedJsonArray(bool $reParse = false): array
    {
        if (isset($this->stringDataHasParsed['json']) && !$reParse) {
            return $this->stringDataHasParsed['json'];
        } else {
            return $this->stringDataHasParsed['json'] =
                DataParser::stringToJsonArray($this->stringDataIsWaitingToBeParsed);
        }
    }

    public function getParsedJsonObject(bool $reParse = false): object
    {
        if (isset($this->stringDataHasParsed['jsonObject']) && !$reParse) {
            return $this->stringDataHasParsed['jsonObject'];
        } else {
            return $this->stringDataHasParsed['jsonObject'] =
                DataParser::stringToJsonObject($this->stringDataIsWaitingToBeParsed);
        }
    }

    public function getParsedQueryArray(bool $reParse = false): array
    {
        if (isset($this->stringDataHasParsed['query']) && !$reParse) {
            return $this->stringDataHasParsed['query'];
        } else {
            return $this->stringDataHasParsed['query'] =
                DataParser::stringToQueryArray($this->stringDataIsWaitingToBeParsed);
        }
    }

    public function getParsedXmlObject(bool $reParse = false): \SimpleXMLElement
    {
        if (isset($this->stringDataHasParsed['xml']) && !$reParse) {
            return $this->stringDataHasParsed['xml'];
        } else {
            return $this->stringDataHasParsed['xml'] =
                DataParser::stringToXmlObject($this->stringDataIsWaitingToBeParsed);
        }
    }

    public function getParsedHtmlObject(bool $reParse = false): \DOMDocument
    {
        if (isset($this->stringDataHasParsed['html']) && !$reParse) {
            return $this->stringDataHasParsed['html'];
        } else {
            return $this->stringDataHasParsed['html'] =
                DataParser::stringToHtmlObject($this->stringDataIsWaitingToBeParsed);
        }
    }

}