<?php

namespace WakeOnWeb\SalesforceClient\DTO;

/**
 * SalesforceObject.
 *
 * @author Stephane PY <s.py@wakeonweb.com>
 */
class SalesforceObject
{
    private $attributes = [];
    private $fields = [];

    private function __construct(array $attributes, array $fields)
    {
        $this->attributes = $attributes;
        $this->fields = $fields;
    }

    public static function createFromArray(array $data)
    {
        $attributes = array_key_exists('attributes', $data) ? (array) $data['attributes'] : [];
        unset($data['attributes']);

        return new self($attributes, $data);
    }

    public function getType()
    {
        return $this->getAttribute('type');
    }

    public function getUrl()
    {
        return $this->getAttribute('url');
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($key, $default = null)
    {
        return $this->hasAttribute($key) ? $this->attributes[$key] : $default;
    }

    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getField($key, $default = null)
    {
        return $this->hasField($key) ? $this->fields[$key] : $default;
    }

    public function hasField($key)
    {
        return array_key_exists($key, $this->fields);
    }
}
