<?php

namespace Finwo\DataFile;

class DataFile
{
    public static $supported = array(
        'php',
        'json',
        'yml',
        'yaml',
        'csv',
        'pev',
    );

    protected static function set_deep($path, &$dataHolder = array(), $value = null)
    {
        $keys = explode('.', $path);
        while (count($keys)) {
            $dataHolder = &$dataHolder[array_shift($keys)];
        }
        $dataHolder = $value;
    }

    protected static function percentEncode($input)
    {
        return implode("\n", str_split(http_build_query(array(
            "t" => gettype($input),
            "v" => $input,
        )), 70));
    }

    protected static function percentDecode($input)
    {
        // Variable may be too large for str_parse
        $input     = str_replace("\n", "", $input);
        if(!strlen($input)) {
            return array();
        }
        $data      = array();
        $variables = explode('&', $input);
        foreach ($variables as $variable) {
            $components = explode('=', $variable);
            $key        = str_replace(array( '[', ']' ), array( '.', '' ), urldecode(array_shift($components)));
            $value      = urldecode(array_shift($components));
            self::set_deep($key, $data, $value);
        }
        switch ($data['t']) {
            case 'boolean':
                return filter_var($data['v'], FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return intval($data['v']);
            default:
                return $data['v'];
        }
    }

    public static function read($filename)
    {
        $extension = @strtolower(array_pop(explode('.', $filename)));
        switch ($extension) {
            case 'php':
                return require_once($filename);
            case 'json':
                return json_decode(file_get_contents($filename), true);
            case 'yml':
            case 'yaml':
                return \Spyc::YAMLLoad($filename);
            case 'csv':
                $fp      = fopen($filename, 'r', false);
                $data    = array();
                $headers = str_getcsv(fgets($fp));
                while (($row = fgetcsv($fp)) !== false) {
                    array_push($data, array_combine($headers, $row));
                }
                fclose($fp);

                return $data;
            case 'pev':
                return self::percentDecode(@file_get_contents($filename));
                break;
            default:
                return null;
        }
    }

    public static function write($filename, $data = array())
    {
        // Detect extension
        $extension = @strtolower(array_pop(explode('.', $filename)));

        // Make sure the directory exists
        $dir = dirname($filename);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        switch ($extension) {
            case 'php':
                return file_put_contents($filename, '<?php return ' . var_export($data, true) . ';') !== false;
                break;
            case 'json':
                return file_put_contents($filename, json_encode($data)) !== false;
            case 'yml':
            case 'yaml':
                return file_put_contents($filename, \Spyc::YAMLDump($data, false, false, true)) !== false;
            case 'csv':
                // Fetch keys
                $headers = array();
                foreach ($data as $row) {
                    $headers = array_merge($headers, array_keys($row));
                }
                $headers = array_unique($headers);
                $output  = 0;

                // Write data
                $fp = fopen($filename, 'w');
                foreach ($data as $row) {
                    $output = ($output === false) ? $output : fputcsv(
                        $fp,
                        array_map(function ($column) use ($row) {
                            if (isset($row[$column])) {
                                return $row[$column];
                            }

                            return null;
                        }, $headers));
                }
                fclose($fp);

                return $output;
            case 'pev':
                return file_put_contents($filename, self::percentEncode($data)) !== false;
                break;
            default:
                return null;
        }
    }
}
