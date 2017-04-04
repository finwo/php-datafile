<?php

namespace Finwo\DataFile;

use Finwo\DataFile\Format\FormatInterface;
use Finwo\DataFile\Storage\StorageInterface;

class DataFile
{
    public static $supported = array(
//        'json',
    );

    protected static $formatMap = array(
//        'json' => 'Finwo\\DataFile\\Format\\JsonFormat',
    );

    protected static $storageDrivers = array(
//        'Finwo\\DataFile\\Storage\\LocalStorage',
    );

    /**
     * @param string $identifier
     *
     * @return bool|string
     */
    protected static function getDriver( $identifier )
    {
        foreach (self::$storageDrivers as $storageDriver) {
            if(forward_static_call(array($storageDriver, 'supports'), $identifier)) {
                return $storageDriver;
            }
        }
        return false;
    }

    /**
     * @param string $format
     *
     * @return bool
     */
    public static function registerFormat( $format )
    {
        $object = new $format();
        if(!($object instanceof FormatInterface)) {
            return false;
        }

        foreach ( forward_static_call(array($format, 'getFormats')) as $type ) {
            array_push(self::$supported, $type);
            self::$formatMap[$type] = $format;
        }

        return true;
    }

    /**
     * @param string $storage
     *
     * @return bool
     */
    public static function registerStorage( $storage )
    {
        $object = new $storage();
        if(!($object instanceof StorageInterface)) {
            return false;
        }
        array_push(self::$storageDrivers, $storage);
        return true;
    }

    /**
     * Initialize all direct-access variables
     * This gets called as soon as this file is loaded
     */
    public static function init()
    {

        // Initialize format drivers
        foreach (glob(implode(DIRECTORY_SEPARATOR, array(__DIR__,'Format','*.php'))) as $filename) {

            $filename = explode(DIRECTORY_SEPARATOR, $filename);
            $filename = explode('.',array_pop($filename));
            array_pop($filename);
            $filename = implode('.', $filename);

            if($filename == 'FormatInterface') continue;

            $className = explode("\\",__CLASS__);
            array_pop($className);
            array_push($className, 'Format');
            array_push($className, $filename);
            $className = implode('\\', $className);

            self::registerFormat($className);
        }

        // Initialize storage drivers
        foreach (glob(implode(DIRECTORY_SEPARATOR, array(__DIR__,'Storage','*.php'))) as $filename) {

            $filename = explode(DIRECTORY_SEPARATOR, $filename);
            $filename = explode('.',array_pop($filename));
            array_pop($filename);
            $filename = implode('.', $filename);

            if($filename == 'StorageInterface') continue;

            $className = explode("\\",__CLASS__);
            array_pop($className);
            array_push($className, 'Storage');
            array_push($className, $filename);
            $className = implode('\\', $className);

            self::registerStorage($className);
        }
    }

    /**
     * @param string $identifier
     *
     * @return mixed|null
     */
    public static function read( $identifier )
    {
        // Fetch which storage driver to use
        $driver = self::getDriver($identifier);
        if (!$driver) return null;

        // Fetch the data type & formatter
        $type = forward_static_call(array($driver, 'getType'), $identifier);
        if(!in_array($type, self::$supported)) return null;
        $formatter = self::$formatMap[$type];

        // Fetch the contents of the identifier
        $contents = forward_static_call(array($driver, 'read'), $identifier);
        if(!$contents) return null;

        // Decode the data
        return forward_static_call(array($formatter, 'decode'), $contents);
    }

    /**
     * @param string $identifier
     * @param array  $data
     *
     * @return bool|null
     */
    public static function write($identifier, $data = array())
    {
        // Fetch which storage driver to use
        $driver = self::getDriver($identifier);
        if (!$driver) return null;

        // Fetch the data type & formatter
        $type = forward_static_call(array($driver, 'getType'), $identifier);
        if(!in_array($type, self::$supported)) return null;
        $formatter = self::$formatMap[$type];

        // Encode the data
        $contents = forward_static_call(array($formatter, 'encode'), $data);

        // Store it where needed
        return forward_static_call(array($driver, 'write'), $identifier, $contents);
    }
}

DataFile::init();
