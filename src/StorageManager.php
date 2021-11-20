<?php

namespace Hyqo\Memorize;

final class StorageManager
{
    /** @var null|Storage */
    private static $globalStorage = null;

    /** @var null|\SplObjectStorage */
    private static $objectStorage = null;

    public static function globalStorage(): Storage
    {
        if (!self::$globalStorage) {
            self::$globalStorage = new Storage();
        }

        return self::$globalStorage;
    }

    public static function objectStorage(object $object): Storage
    {
        if (!self::$objectStorage) {
            self::$objectStorage = new \SplObjectStorage();
        }

        if (self::$objectStorage->offsetExists($object)) {
            return self::$objectStorage->offsetGet($object);
        }

        $storage = new Storage();
        self::$objectStorage->offsetSet($object, $storage);

        return $storage;
    }
}
