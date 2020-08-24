<?php
/**
 * Copyright: Swlib
 * Author: Twosee <twose@qq.com>
 * Date: 2018/4/15 下午3:25
 */

namespace Swlib\Util;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use SimpleXMLElement;
use DOMDocument;

/**
 * @method static array toJsonArray(string $var)
 * @method static object toJsonObject(string $var)
 * @method static string toJsonString(array | array $var)
 * @method static array toQueryArray(string $var)
 * @method static string toQueryString(array $var)
 * @method static SimpleXMLElement toXmlObject(string | array $var, SimpleXMLElement $xml)
 * @method static string toXmlString(array $var)
 * @method static DOMDocument toDomObject(string $var)
 * @method static string toMultipartString(array $var, string $boundary)
 * @method static array toXmlArray(string $xml)
 */
class DataParser
{
    public static function getCallableMap(): array
    {
        static $callMap;
        if (!isset($callMap)) {
            $reflection = new ReflectionClass(self::class);
            $methods = $reflection->getMethods(ReflectionMethod::IS_STATIC);
            foreach ($methods as $method) {
                if (preg_match('/([a-z]+)To([A-Z][a-z]+)([A-Z][a-z]+)/', $methodName = $method->getName(), $matches)) {
                    $targetFormat = $matches[2];
                    $targetType = $matches[3];
                    $fromType = $matches[1];
                    $subName = '\\' . self::class . '::' . $methodName;
                    $sub = new ReflectionMethod($subName);
                    $subReturnType = $sub->getReturnType();
                    $callName = 'to' . $targetFormat . $targetType;
                    $callMap[$callName]['supports'][$fromType] = $subName;
                    $callMap[$callName]['returnTypes'][] = $subReturnType;
                    $parameters = $method->getParameters();
                    foreach ($parameters as $parameter) {
                        $p_name = $parameter->getName();
                        $p_type = $parameter->getType();
                        $callMap[$callName]['parameters'][$p_name][] = $p_type;
                    }
                }
            }
        }

        return $callMap;
    }

    public static function createComment(): string
    {
        $comments = "/**\n";
        foreach (self::getCallableMap() as $method => $callInfo) {
            $retTypes = implode('|', array_unique($callInfo['returnTypes']));
            $parameters = [];
            foreach ($callInfo['parameters'] as $p_name => $p_types) {
                $parameters[] = (($p_types = implode('|', $p_types)) ? "{$p_types} " : '') . "\${$p_name}";
            }
            $parameters = implode(', ', $parameters);
            $comments .= " * @method static {$retTypes} {$method}($parameters)\n";
        }
        $comments .= '*/';

        return $comments;
    }

    public static function __callStatic($name, $arguments)
    {
        $var = $arguments[0] ?? null;
        if ($var === null) {
            throw new InvalidArgumentException("Argument can't be null!");
        }
        $callMap = static::getCallableMap();
        if (TypeDetector::canBeArray($var) && isset($callMap[$name]['supports']['array'])) {
            return $callMap[$name]['supports']['array'](...$arguments);
        } elseif (TypeDetector::canBeString($var) && isset($callMap[$name]['supports']['string'])) {
            return $callMap[$name]['supports']['string'](...$arguments);
        } elseif (is_object($var) && isset($callMap[$name]['supports']['object'])) {
            return $callMap[$name]['supports']['object'](...$arguments);
        }

        throw new InvalidArgumentException(
            'Not implement for ' . (is_object($var) ? get_class($var) : gettype($var)) . " $name"
        );
    }

    public static function stringToJsonArray(string $var): array
    {
        return (array)json_decode($var, true);
    }

    public static function stringToJsonObject(string $var): object
    {
        return (object)json_decode($var);
    }

    public static function arrayToJsonString(array $var): string
    {
        $var = json_encode($var);
        return ($var !== false && $var !== null) ? $var : '{}';
    }

    public static function objectToJsonString(array $var): string
    {
        $var = json_encode($var);
        return ($var !== false && $var !== null) ? $var : '{}';
    }

    public static function stringToQueryArray(string $var): array
    {
        parse_str($var, $ret);

        return $ret;
    }

    public static function arrayToQueryString(array $var): string
    {
        return http_build_query($var);
    }

    public static function stringToXmlObject(string $var): SimpleXMLElement
    {
        return new SimpleXMLElement($var);
    }

    public static function arrayToXmlString(array $var): string
    {
        return self::arrayToXmlObject($var)->asXML() ?: '';
    }

    public static function arrayToXmlObject(array $var, ?SimpleXMLElement &$xml = null): SimpleXMLElement
    {
        if ($xml === null) {
            $xml = new SimpleXMLElement('<?xml version="1.0"?><root></root>');
        }
        foreach ($var as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item' . $key; //dealing with <0/>..<n/> issues
            }
            if (is_array($value)) {
                $sub_node = $xml->addChild($key);
                self::arrayToXmlObject($value, $sub_node);
            } else {
                $xml->addChild("$key", htmlspecialchars("$value"));
            }
        }

        return $xml;
    }

    public static function stringToDomObject(string $var): DOMDocument
    {
        libxml_use_internal_errors(true);
        $html = new DOMDocument($var);
        $html->loadHTML($var);

        return $html;
    }

    public static function arrayToMultipartString(array $var, string $boundary): string
    {
        $ret = '';
        foreach ($var as $name => $value) {
            $value = (string)$value;
            $ret .= "--{$boundary}\r\nContent-Disposition: form-data; name=\"{$name}\"\r\n\r\n{$value}\r\n";
        }
        $ret .= "--{$boundary}--\r\n";

        return $ret;
    }

    public static function stringToXmlArray(string $xml): array
    {
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        return json_decode(json_encode($xmlstring), true);
    }
}
