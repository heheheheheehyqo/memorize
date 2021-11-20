<?php

namespace Hyqo\Memorize\Test;

use Hyqo\Memorize\StorageManager;
use PHPUnit\Framework\TestCase;

use function Hyqo\Memorize\memorize;

class MemorizeTraitTest extends TestCase
{
    public function test_memorize_with_this()
    {
        $class = new class () {
            private $counter = 0;

            public function getValue(): string
            {
                return memorize(function () {
                    return 'counter: ' . $this->counter++;
                });
            }
        };

        $iterations = 0;

        while ($iterations++ < 1000) {
            $value = $class->getValue();
        }

        $this->assertEquals('counter: 0', $value);

        $reflection = new \ReflectionClass(StorageManager::class);
        /** @var \SplObjectStorage */
        $objectStorage = $reflection->getStaticProperties()['objectStorage'];

        $this->assertTrue($objectStorage->offsetExists($class));

        $storage = $objectStorage->offsetGet($class);
        $reflection = new \ReflectionClass($storage);
        $storageProperty = $reflection->getProperty('storage');
        $storageProperty->setAccessible(true);

        $storageValues = $storageProperty->getValue($storage);

        $this->assertCount(1, $storageValues);
    }

    public function test_memorize_without_this()
    {
        $class = new class () {
            private $counter = 0;

            public function setCounter(int $counter): void
            {
                $this->counter = $counter;
            }

            public function getValue(): string
            {
                $counter = $this->counter;

                return memorize(static function () use ($counter) {
                    return 'counter: ' . $counter;
                });
            }
        };

        $range = [0, 0, 0, 1];

        foreach ($range as $counter) {
            $class->setCounter($counter);
            $class->getValue();
        }


        $reflection = new \ReflectionClass(StorageManager::class);
        $storage = $reflection->getStaticProperties()['globalStorage'];

        $reflection = new \ReflectionClass($storage);
        $storageProperty = $reflection->getProperty('storage');
        $storageProperty->setAccessible(true);

        $storageValues = $storageProperty->getValue($storage);

        $this->assertSameSize(array_unique($range), $storageValues);
    }
}
