<?php

namespace Hyqo\Memorize;

final class Storage
{
    private $storage = [];

    public function has(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    public function get(string $key)
    {
        return $this->storage[$key] ?? null;
    }

    public function set(string $key, $value): void
    {
        $this->storage[$key] = $value;
    }
}
