<?php

namespace Finwo\DataFile\Format;

class YmlFormat implements FormatInterface
{
    /**
     * {@inheritdoc}
     */
    public static function encode($input)
    {
        return \Spyc::YAMLDump($input);
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
