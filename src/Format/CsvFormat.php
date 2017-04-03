<?php

namespace Finwo\DataFile\Format;

class CsvFormat implements FormatInterface
{
    /**
     * {@inheritdoc}
     */
    public static function encode($input)
    {
        $headers = array();
        foreach ($input as $row) {
            $headers = array_merge($headers, array_keys($row));
        }
        $headers = array_unique($headers);

        $fp = fopen('php://temp','r+');
        fputcsv($fp, $headers);
        foreach ($input as $row) {
            fputcsv($fp, array_map(function($key) use ($row) {
                return isset($row[$key]) ? $row[$key] : null;
            }, $headers));
        }

        rewind($fp);
        $output = stream_get_contents($fp);
        fclose($fp);

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public static function decode($input)
    {
        $headers = null;
        $output  = array();
        $index   = 0;

        $fp = fopen('php://temp','r+');
        fwrite($fp, $input);
        rewind($fp);
        while(($row=fgetcsv($fp, 0, ','))!==false) {
            if(is_null($headers)) {
                $headers = $row;
                continue;
            }
            $output[$index++] = array_combine($headers, $row);
        }
        fclose($fp);
        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public static function getFormats()
    {
        return array(
            'csv',
        );
    }
}
