<?php

namespace Finwo\DataFile\Format;

class PhpFormat implements FormatInterface
{
    /**
     * {@inheritdoc}
     */
    public static function encode($input)
    {
        return '<?php'.PHP_EOL.'return '.var_export($input,true).';';
    }

    /**
     * {@inheritdoc}
     */
    public static function decode($input)
    {
        $prefix = '<?php';
        if(!substr($input, 0, strlen($prefix)) == $prefix) return null;
        return eval(substr($input, strlen($prefix)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getFormats()
    {
        return array(
            'php',
        );
    }
}
