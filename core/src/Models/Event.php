<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Models;

class Event extends \xPDOObject
{
    public function save($cacheFlag = null)
    {
        $isNew = $this->isNew();

        if ($isNew) {
            if (empty(parent::get('created_at'))) {
                parent::set('created_at', \time());
            }
        } else {
            parent::set('updated_at', \time());
        }

        return parent::save($cacheFlag);
    }

    public function get($k, $format = null, $formatTemplate = null)
    {
        if (\is_string($k) && 'data' === $k) {
            if (\array_key_exists($k, $this->_fields)) {
                if ($this->isLazy($k)) {
                    $this->_loadFieldData($k);
                }

                $value = $this->_fields[$k];
            }
        } else {
            $value = parent::get($k, $format, $formatTemplate);
        }

        return $value;
    }

    public function setFlagSended(): self
    {
        parent::set('sended', true);
        parent::set('sended_at', \time());

        return $this;
    }

    public function setFlagRejected(): self
    {
        parent::set('rejected', true);

        return $this;
    }

    public function setErrorMessage(string $error): self
    {
        parent::set('error', $error);

        return $this;
    }
}
