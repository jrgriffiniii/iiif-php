<?php

namespace IIIF\Model;

/**
 * Trait providing methods for Classes modeling <a href="http://iiif.io/api/presentation/2.1/#descriptive-properties">metadata properties</a>
 *
 */
trait WithMetaData
{
    protected $metaData = []; //**< @var array the metadata values mapped using labels for keys

    /**
     * This is required for twig templates since they do not support get.
     * @param string $name label of the metadata pair
     * @return string
     */
    public function __call($name, $arguments)
    {
        return $this->__get($name);
    }

    /**
     * Default to the metadata array before attempting to invoke an accessor method
     * @param string $name label of the metadata pair
     * @return string|null
     */
    public function __get($name)
    {
        if (isset($this->metaData[$name])) {
            return $this->metaData[$name];
        }

        return null;
    }

    /**
     * Accessor for the metadata array
     * @return array
     */
    public function getMetaData() : array
    {
        return $this->metaData && is_array($this->metaData) ? $this->metaData : [];
    }

    /**
     * Appends a pair of metadata values to the existing array
     * (Overrides any existing metadata pairs keyed to the same label)
     * @param array $metaData pair of metadata values keyed to labels being added
     * @return object
     */
    public function addMetaData($metaData)
    {
        foreach ($metaData as $name => $value) {
            $this->metaData[$name] = $value;
        }

        return $this;
    }

    /**
     * Replaces or merges a pair of metadata values to the array of a cloned object
     * @param array $metaData pair of metadata values keyed to labels replacing or being merged with the cloned metadata values
     * @param boolean $merge whether to merge or replace with the cloned object (defaults to true)
     * @return object
     */
    public function withMetaData($metaData, $merge = true)
    {
        $model = clone $this;
        $model->metaData = $merge ?
            array_merge($this->getMetaData(), $metaData) :
            $metaData;

        return $model;
    }
}
