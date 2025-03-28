<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Config;

use Vgrish\MindBox\MS2\WorkerInterface;

class WorkersConfig
{
    public function __construct(
        private readonly array $handlers = [],
    ) {
        foreach ($this->handlers as $event => $classes) {
            if (!\is_string($event)) {
                throw new \InvalidArgumentException(\sprintf('Event `%s` does not exist', $event));
            }

            foreach ($classes as $class) {
                if (!\class_exists($class)) {
                    throw new \InvalidArgumentException(\sprintf('Class `%s` does not exist', $class));
                }
            }
        }
    }

    /**
     * @param array{string:array{int:WorkerInterface}} $config
     *
     * @return static
     */
    public static function fromArray(array $config): self
    {
        return new self($config);
    }

    public function getHandlersForEvent(string $event): array
    {
        return $this->handlers[$event] ?? [];
    }

    public function getAllHandlers(): array
    {
        return $this->handlers;
    }

    public function withHandler(string $event, string $handlerClass): self
    {
        if (!\class_exists($handlerClass)) {
            throw new \InvalidArgumentException(\sprintf('Class `%s` does not exist', $handlerClass));
        }

        $newHandlers = $this->handlers;
        $newHandlers[$event][] = $handlerClass;

        return new self($newHandlers);
    }
}
