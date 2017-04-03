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
    public static function encode($input)
    {
        return implode(PHP_EOL, str_split(http_build_query($input), 70));
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
        if(substr_count($input,'&')<intval(ini_get('max_input_vars'))) {
            parse_str($input, $data);
        } else {
            $data      = array();
            $variables = explode('&',$input);
            foreach ($variables as $variable) {
                $components = explode('=', $variable);
                $key        = str_replace(']', '', urldecode(array_shift($components)));
                $value      = urldecode(array_shift($components));
                self::set_deep($key, $data, $value);
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
