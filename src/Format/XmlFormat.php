<?php

namespace Finwo\DataFile\Format;

class XmlFormat implements FormatInterface
{
    protected static $indent = '  ';

    protected static function xml2array(\SimpleXMLElement $parent)
    {
        $output = array();

        // Combine, assume everything is plural
        foreach ($parent as $name => $element) {
            if (!array_key_exists($name, $output)) {
                $output[$name] = array();
            }
            array_push($output[$name], $element->count() ? self::xml2array($element) : trim($element));
        }

        // Deduplicate singletons
        foreach ($output as $name => $values) {
            if (count($values) === 1) {
                $output[$name] = array_pop($values);
            }
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
            if (is_numeric($value))                                $value = floatval($value);
            if (is_float($value) && ( $value === floor($value) ) ) $value = intval($value);
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
    public static function encode($input, $indent = '')
    {
        if(is_numeric($indent)) {
            $indent = str_repeat(self::$indent,intval($indent));
        }
        if(!is_string($indent)) {
            $indent = '';
        }

        $output = '';

        // Add header if needed
        if(!strlen($indent)) {
            $output .= '<?xml version=\'1.0\' encoding="utf-8" ?>'.PHP_EOL;
            $output .= '<data>';
            $indent = self::$indent;
        }

        // Check what to do
        switch(gettype($input)) {
            case 'object':
                if (method_exists($input, 'toArray')) {
                    $input = $input->toArray();
                } elseif (method_exists($input,'__toArray')) {
                    $input = $input->__toArray();
                } else {
                    $input = (array) $input;
                }
            case 'array':
                foreach ($input as $key => $value) {
                    if(strpos('0123456789',substr($key,0,1))!==false) $key = '_'.$key;
                    $output .= PHP_EOL . $indent . '<' . $key . '>';
                    $output .= self::encode($value, $indent . self::$indent);
                    if(in_array(gettype($value),array('array','object'))) $output .= PHP_EOL . $indent;
                    $output .= '</' . $key . '>';
                }
                break;
            case 'boolean':
                $output .= $input ? 'true' : 'false';
                break;
            default:
                $output .= $input;
                break;

        }

        // Add footer if needed
        if($indent === self::$indent) {
            $output .= PHP_EOL.'</data>'.PHP_EOL;
        }

        return $output;
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
