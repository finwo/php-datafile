<?php

namespace Finwo\DataFile\Format;

class YmlFormat implements FormatInterface
{
    /**
     * {@inheritdoc}
     */
    public static function encode($input)
    {
        //YAMLDump($array, $indent = false, $wordwrap = false, $no_opening_dashes = false)
        return \Spyc::YAMLDump($input, 2, false, true);
    }

    /**
     * {@inheritdoc}
     */
    public static function decode($input)
    {
        return \Spyc::YAMLLoadString($input);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFormats()
    {
        return array(
            'yml',
            'yaml',
        );
    }
}
