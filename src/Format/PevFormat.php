<?php

namespace Finwo\DataFile\Format;

class PevFormat implements FormatInterface
{
    protected static function set_deep($path, &$dataHolder = array(), $value = null)
    {
        $keys = explode('[', $path);
        while (count($keys)) {
            $dataHolder = &$dataHolder[array_shift($keys)];
        }
        $dataHolder = $value;
    }

    /**
     * {@inheritdoc}
     */
    public static function encode($input, $parentKey = '')
    {
        if ( !in_array(gettype($input), array( 'object', 'array' )) && !strlen($parentKey) ) {
            $input = array(
                't' => gettype($input),
                'v' => $input,
            );
        }

        $output = '';
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
                    $compositeKey = strlen($parentKey) ? $parentKey.'['.$key.']' : $key;
                    if(strlen($output)) $output .= '&';
                    $output .= self::encode($value, $compositeKey);
                }
                break;
            case 'null':
                return urlencode($parentKey) . '=null';
            case 'boolean':
                return urlencode($parentKey) . '=' . ( $input ? 'true' : 'false' );
            default:
                return urlencode($parentKey) . '=' . urlencode( '' . $input );
        }

        if(!strlen($parentKey)) {
            $output = implode(PHP_EOL,str_split($output,70));
        }

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public static function decode($input)
    {
        // Variable may be too large for str_parse
        $input = str_replace("\r\n", '', $input);
        $input = str_replace("\r"  , '', $input);
        $input = str_replace("\n"  , '', $input);

        // An easy way out
        if(!strlen($input)) {
            return array();
        }

        // Decode the input
        $data      = array();
        $variables = explode('&',$input);
        foreach ($variables as $variable) {

            $components = explode('=', $variable);
            $key        = str_replace(']', '', urldecode(array_shift($components)));
            $value      = urldecode(array_shift($components));

            // Fix the value format as needed
            if (is_numeric($value))                                $value = floatval($value);
            if (is_float($value) && ( $value === floor($value) ) ) $value = intval($value);
            if ($value==='false') $value = false;
            if ($value==='true')  $value = true;

            self::set_deep($key, $data, $value);
        }

        // Allow for the 'old' format
        if( implode('|',array_keys($data)) == 't|v' ) {
            $type = $data['t'];
            $data = $data['v'];
            switch($type) {
                case 'boolean':
                    $data = filter_var($data, FILTER_VALIDATE_BOOLEAN);
                    break;
                case 'integer':
                    $data = intval($data);
                    break;
                case 'double':
                case 'float':
                    $data = floatval($data);
                    break;
                default:
                    // Do nothing
                    break;
            }
        }

        // & return what was given
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public static function getFormats()
    {
        return array(
            'pev',
        );
    }
}
