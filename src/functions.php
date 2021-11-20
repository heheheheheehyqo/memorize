<?php

namespace Hyqo\Memorize;

function memorize(\Closure $closure, ?string $key = null)
{
    $reflection = new \ReflectionFunction($closure);
    $object = $reflection->getClosureThis();

    if ($object) {
        $storage = StorageManager::objectStorage($object);
    } else {
        $storage = StorageManager::globalStorage();
    }

    $id = md5(closureId($reflection) . stringifyParameters($reflection));

    if (!$storage->has($id)) {
        $storage->set($id, $closure());
    }

    return $storage->get($id);
}

function closureId(\ReflectionFunction $reflection): string
{
    return $reflection->getFileName() . '-' . $reflection->getStartLine() . '-' . $reflection->getEndLine();
}

function stringifyParameters(\ReflectionFunction $reflection): string
{
    $serializedValues = [];

    foreach ($reflection->getStaticVariables() as $key => $value) {
        switch (true) {
            case $value instanceof \Closure:
                $reflection = new \ReflectionFunction($value);
                $serializedValue = sprintf('%s(%s)', closureId($reflection), stringifyParameters($reflection));
                break;
            default:
                try {
                    $serializedValue = serialize($value);
                } catch (\Exception $e) {
                    $serializedValue = '';
                }
                break;
        }
        $serializedValues[] = sprintf('{key:%s,value:%s}', $key, $serializedValue);
    }

    return implode(' ', $serializedValues);
}
