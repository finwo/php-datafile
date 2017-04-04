<?php

/*
 * TODO: Differentiate between data & table formats
 */

namespace Finwo\DataFile\Format;

interface FormatInterface
{
    /**
     * Encode data into a string format
     *
     * @param mixed $input
     *
     * @return string
     */
    public static function encode($input);

    /**
     * Decode data from a string format
     *
     * @param string $input
     *
     * @return mixed
     */
    public static function decode($input);

    /**
     * Get a list of supported formats for this data type.
     *
     * @return array
     */
    public static function getFormats();
}
