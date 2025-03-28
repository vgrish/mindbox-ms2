<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Config;

class ExtensionsConfig
{
    public function __construct(
        private readonly array $handlers = [],
    ) {
        foreach ($this->handlers as $extension => $callable) {
            if (!\is_string($extension)) {
                throw new \InvalidArgumentException(\sprintf('Extension `%s` does not exist', $extension));
            }

            if (!\is_callable($callable)) {
                throw new \InvalidArgumentException(\sprintf('Extension `%s` not called', $extension));
            }
        }
    }

    /**
     * @param array{string:callable} $config
     *
     * @return static
     */
    public static function fromArray(array $config): self
    {
        return new self($config);
    }

    public function getHandlersForExtension(string $extension): ?\Closure
    {
        return $this->handlers[$extension] ?? null;
    }

    public function getAllHandlers(): array
    {
        return $this->handlers;
    }

    public function withHandler(string $extension, callable $callable): self
    {
        if (!\is_callable($callable)) {
            throw new \InvalidArgumentException(\sprintf('Extension `%s` not called', $extension));
        }

        $newHandlers = $this->handlers;
        $newHandlers[$extension] = $callable;

        return new self($newHandlers);
    }
}
