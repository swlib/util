<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/14 下午11:18
 */

namespace Swlib\Util;

use DOMDocument;
use InvalidArgumentException;
use SimpleXMLElement;

trait StringDataParserTrait
{
    protected $stringDataIsWaitingToBeParsed;
    protected $stringDataHasParsed = [];

    protected function __constructStringDataParser(&$data)
    {
        if (isset($data)) {
            if (!is_string($data) && !(is_object($data) && method_exists($data, '__toString'))) {
                throw new InvalidArgumentException('Bind data must be string type or has toString method.');
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

    public function getParsedXmlArray(bool $reParse = false): array
    {
        if (isset($this->stringDataHasParsed['xml']) && !$reParse) {
            return $this->stringDataHasParsed['xml'];
        } else {
            return $this->stringDataHasParsed['xml'] =
                json_decode(
                    json_encode(
                        simplexml_load_string(
                            $this->stringDataIsWaitingToBeParsed,
                            "SimpleXMLElement",
                            LIBXML_NOCDATA
                        )
                    ),
                    true
                );
        }
    }

    public function getParsedXmlObject(bool $reParse = false): SimpleXMLElement
    {
        if (isset($this->stringDataHasParsed['xml']) && !$reParse) {
            return $this->stringDataHasParsed['xml'];
        } else {
            return $this->stringDataHasParsed['xml'] =
                DataParser::stringToXmlObject($this->stringDataIsWaitingToBeParsed);
        }
    }

    public function getParsedDomObject(bool $reParse = false): DOMDocument
    {
        if (isset($this->stringDataHasParsed['html']) && !$reParse) {
            return $this->stringDataHasParsed['html'];
        } else {
            return $this->stringDataHasParsed['html'] =
                DataParser::stringToDomObject($this->stringDataIsWaitingToBeParsed);
        }
    }

    public function getDataContain(string $needle, int $offset = 0): bool
    {
        return strpos($this->stringDataIsWaitingToBeParsed, $needle, $offset) !== false;
    }

    /**
     * @param string $regex
     * @param int|string $group
     * @param int $fill_size Fill the array to the fixed size
     *
     * @return array|string
     */
    public function getDataRegexMatch(string $regex, $group = null, int $fill_size = 0)
    {
        $is_matched = preg_match($regex, $this->stringDataIsWaitingToBeParsed, $matches);

        if ($group !== null && $group >= 0) {
            if ($is_matched) {
                return $matches[$group] ?? '';
            } else {
                return '';
            }
        } else {
            if ($is_matched) {
                if ($group !== null) {
                    array_shift($matches);
                }
                return $matches ?: [];
            } else {
                return $fill_size > 0 ? array_fill(0, $fill_size, '') : $matches;
            }
        }
    }

    public function getDataRegexMatches(string $regex, int $flag): array
    {
        return preg_match_all($regex, $this->stringDataIsWaitingToBeParsed, $matches, $flag) ? $matches : [];
    }
}
