<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Config;

use Vgrish\MindBox\MS2\WebHookInterface;

class WebHooksConfig
{
    public function __construct(
        private readonly array $handlers = [],
    ) {
        foreach ($this->handlers as $operation => $classes) {
            if (!\is_string($operation)) {
                throw new \InvalidArgumentException(\sprintf('Operation `%s` does not exist', $operation));
            }

            foreach ($classes as $class) {
                if (!\class_exists($class)) {
                    throw new \InvalidArgumentException(\sprintf('Class `%s` does not exist', $class));
                }
            }
        }
    }

    /**
     * @param array{string:array{int:WebHookInterface}} $config
     *
     * @return static
     */
    public static function fromArray(array $config): self
    {
        return new self($config);
    }

    public function getHandlersForOperation(string $operation): array
    {
        return $this->handlers[$operation] ?? [];
    }

    public function getAllHandlers(): array
    {
        return $this->handlers;
    }

    public function withHandler(string $operation, string $handlerClass): self
    {
        if (!\class_exists($handlerClass)) {
            throw new \InvalidArgumentException(\sprintf('Class `%s` does not exist', $handlerClass));
        }

        $newHandlers = $this->handlers;
        $newHandlers[$operation][] = $handlerClass;

        return new self($newHandlers);
    }
}
