<?php

namespace Finwo\DataFile\Format;

class JsonFormat implements FormatInterface
{
    /**
     * {@inheritdoc}
     */
    public static function encode($input)
    {
        return json_encode($input);
    }

    /**
     * {@inheritdoc}
     */
    public static function decode($input)
    {
        return json_decode($input, true);
    }

    /**
     * {@inheritdoc}
     */
    public static function getFormats()
    {
        return array(
            'json',
        );
    }
}
