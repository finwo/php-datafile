<?php

namespace Finwo\DataFile\Storage;

interface StorageInterface
{
    /**
     * @param string $identifier
     * @param string $data
     *
     * @return bool
     */
    public static function write($identifier, $data); // Create / Update

    /**
     * @param string $identifier
     *
     * @return string|bool
     */
    public static function read($identifier);         // Read

    /**
     * @param string $identifier
     *
     * @return bool
     */
    public static function delete($identifier);       // Delete

    /**
     * @param $identifier
     *
     * @return string
     */
    public static function getType($identifier);

    /**
     * @param $identifier
     *
     * @return bool
     */
    public static function supports($identifier);
}
