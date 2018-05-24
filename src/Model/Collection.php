<?php

namespace IIIF\Model;

/**
 * Class modeling collections of manifests within the <a href ="http://iiif.io/api/presentation/2.0/#collections">IIIF Presentation API</a>
 */
class Collection
{
    use WithMetaData; /**< Mixes in the WithMetaData Trait */

    private $id; /**< @var string URI for the Collection */
    private $manifests; /**< @var array<Manifest> Manifests contained within the Collection */
    private $label; /**< @var string label provided for the collection */
    private $description; /**< @var string description provided for the collection */
    private $attribution; /**< @var string parties attributed to the collection */
    private $metadata; /**< @var array metadata values for the collection */

    const TYPE = 'sc:collection'; /**< @var string the namespaced type identifier for this resource */

    /**
     * Determines whether or not an array of values serializes a collection
     * @return boolean
     */
    public static function isCollection(array $data)
    {
        return strtolower($data['@type']) === self::TYPE;
    }

    /**
     * Constructor
     * @param string $id the URI for the Collection
     * @param string $label provided for the collection
     * @param string $description description provided for the collection
     * @param string $attribution parties attributed to the collection
     * @param array<Manifest> Manifests contained within the Collection
     * @param array metadata values for the collection
     */
    public function __construct(
        string $id,
        string $label = null,
        string $description = null,
        string $attribution = null,
        array $manifests = [],
        array $metadata = null
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->description = $description;
        $this->attribution = $attribution;
        $this->manifests = $manifests;
        $this->metadata = $metadata;
    }

    /**
     * Accessor method for the label
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

   /**
    * Construct an object from a string of a JSON-serialized Collection
    * @param string $json string containing the JSON-serialized values
    * @return Collection
    */
    public static function fromJson(string $json)
    {
        return static::fromArray(json_decode($json, true));
    }

    /**
     * Given an array containing a serialized Collection, retrieve its label
     * @param array $data serialized Collection values
     * @return string|null
     */
    private static function getLabelFromData($data)
    {
        if (is_string($data)) {
            return $data;
        }
        if (isset($data['@value'])) {
            return $data['@value'];
        }
        if (isset($data[0]['@value'])) {
            return $data[0]['@value'];
        }

        return null;
    }

    /**
     * Given an array containing a serialized Collection, retrieve its serialized Manifests
     * @param array $data serialized Collection values
     * @return array
     */
    private static function getManifestsFromData($data)
    {
        if (isset($data['members'])) {
            return $data['members'];
        }
        if (isset($data['manifests'])) {
            return $data['manifests'];
        }

        return [];
    }

    /**
     * Accessor method for the Manifests in this Collection
     * @return array<Manifest>
     */
    public function getManifests()
    {
        return $this->manifests;
    }

    /**
     * Access method for the URI of this Collection
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Construct an object from an array of values
     * @param array $data array of values being used to construct a Collection
     * @return Collection
     */
    public static function fromArray(array $data)
    {
        return new static(
            $data['@id'],
            static::getLabelFromData($data['label'] ?? []),
            $data['description'] ?? null,
            $data['attribution'] ?? null,
            array_map(function ($manifest) {
                return LazyManifest::fromArray($manifest);
            }, static::getManifestsFromData($data))
        );
    }

    /**
     * Provide a callback used to deserialize all lazy-loaded Manifests within this Collection
     * @see LazyManifest::setLoader()
     * @param callable $loader the callback used to deserialize the LazyManifest
     */
    public function setManifestLoader(callable $loader)
    {
        $manifests = $this->getManifests();
        foreach ($manifests as $manifest) {
            /* @var LazyManifest $manifest */
            $manifest->setLoader($loader);
        }
    }
}
