<?php

namespace Finwo\DataFile\Storage;

class LocalStorage implements StorageInterface
{
    /**
     * {@inheritdoc}
     */
    public static function write($identifier, $data)
    {
        return file_put_contents($identifier, $data);
    }

    /**
     * {@inheritdoc}
     */
    public static function read($identifier)
    {
        $path = self::truepath($identifier);
        return is_file($path) ? file_get_contents($path) : false;
    }

    /**
     * {@inheritdoc}
     */
    public static function delete($identifier)
    {
        return unlink($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public static function getType($identifier)
    {
        $identifier = explode('.', $identifier);
        return array_pop($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public static function supports($identifier)
    {
        return !!self::truepath($identifier);
    }

    /**
     * See: http://stackoverflow.com/a/4050444/2928176
     *
     * @param string $path
     *
     * @return string|bool
     */
    protected static function truepath($path)
    {
        // whether $path is unix or not
        $unipath = strlen($path) == 0 || $path{0} != '/';
        // attempts to detect if path is relative in which case, add cwd
        if (strpos($path, ':') === false && $unipath) {
            $path = getcwd() . DIRECTORY_SEPARATOR . $path;
        }
        // resolve path parts (single dot, double dot and double delimiters)
        $path      = str_replace(array( '/', '\\' ), DIRECTORY_SEPARATOR, $path);
        $parts     = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $path = implode(DIRECTORY_SEPARATOR, $absolutes);
        // resolve any symlinks
        if (file_exists($path) && linkinfo($path) > 0) {
            $path = readlink($path);
        }
        // put initial separator that could have been lost
        $path = !$unipath ? '/' . $path : $path;

        return $path;
    }
}
