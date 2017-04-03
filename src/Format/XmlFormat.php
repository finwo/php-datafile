<?php

namespace Finwo\DataFile\Format;

class XmlFormat implements FormatInterface
{
    protected static function xml2array(\SimpleXMLElement $parent)
    {
        $output = array();
        foreach ($parent as $name => $element) {
            ($node = &$output[$name])
            && (1 === count($node) ? $node = array( $node ) : 1)
            && $node = &$node[];
            $node = $element->count() ? self::xml2array($element) : trim($element);
        }

        return $output;
    }

    protected static function convertData(array $data)
    {
        $output = array();
        foreach ($data as $key => $value) {

            // Fix key
            if (substr($key, 0, 1) == '_')           $key = substr($key, 1);
            if (is_numeric($key))                    $key = floatval($key);
            if (is_float($key) && (($key % 1) == 0)) $key = intval($key);

            // Fix value
            if (is_numeric($value))                      $value = floatval($value);
            if (is_float($value) && (($value % 1) == 0)) $value = intval($value);
            if ($value==='false') $value = false;
            if ($value==='true')  $value = true;

            // Iterate down or save
            switch (gettype($value)) {
                case 'array':
                case 'object':
                    $output[$key] = self::convertData($value);
                    break;
                default:
                    $output[$key] = $value;
            }
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public static function encode($input)
    {

    }

    /**
     * {@inheritdoc}
     */
    public static function decode($input)
    {
        return self::convertData(self::xml2array(simplexml_load_string($input)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getFormats()
    {
        return array(
            'xml',
        );
    }
}