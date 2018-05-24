<?php

namespace IIIF\Model;

/**
 * Class modeling manifests for image resources within the <a href ="http://iiif.io/api/presentation/2.1/#manifest">IIIF Presentation API</a>
 */
class Manifest
{
    use WithMetaData; /**< Mixes in the WithMetaData Trait */

    protected $label; /**< @var string label provided for the manifested image resource */
    protected $sequences; /**< @var array<Sequence> array of Sequence objects defining the order in which these are structured */
    protected $id; /**< @var string the URI for the manifest */
    const TYPE = 'sc:manifest'; /**< @var string the namespaced type identifier for this resource */

    /**
     * Constructor
     * @param string $id URI for the manifest
     * @param string $label label provided for the manifest
     * @param array<Sequence> sequences in which this manifest is structured
     */
    public function __construct(
        string $id,
        string $label = null,
        array $sequences
    ) {
        $this->label = $label;
        $this->sequences = $sequences;
        $this->id = $id;
    }

    /**
     * Accessor method for the manifest URI
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Static method for determining whether or an array of values has serialized a manifest
     * @param array $data the serialized manifest
     * @return boolean
     */
    public static function isManifest(array $data)
    {
        return strtolower($data['@type']) === self::TYPE;
    }

    /**
     * Construct an object from an array of values
     * @param array $data array of values being used to construct a Manifest
     * @return Manifest
     */
    public static function fromArray(array $data): self
    {
        return new static(
            $data['@id'],
            $data['label'] ?? '',
            array_map(function ($sequence) {
                return Sequence::fromArray($sequence);
            }, $data['sequences'] ?? [])
        );
    }

    /**
     * Construct an object from a string of a JSON-serialized Manifest
     * @param string $json string containing the JSON-serialized values
     * @return Manifest
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        return static::fromArray($data);
    }

    /**
     * Generate the canonical URL for a given URI
     * @see getCanvasRegionFromUrl()
     * @param string $uri the URI
     */
    public function getCanonicalUrl($uri)
    {
        $segments = explode('#', $uri);
        array_pop($segments);

        return implode('#', $segments);
    }

    /**
     * Retrieve the region on a canvas for a given URI
     * @param string $uri the URI
     * @return Region
     */
    public function getCanvasRegionFromUrl($uri)
    {
        $region = Region::fromUrlTarget($uri);
        $canonicalUri = $this->getCanonicalUrl($uri);
        if (!$this->containsCanvas($canonicalUri)) {
            return null;
        }
        $canvas = $this->getCanvas($canonicalUri);

        return $canvas->getRegion($region);
    }

    /**
     * Accessor method for the label
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label ?? '';
    }

    /**
     * Retrieve a canvas using its index from the default sequence for this manifest
     * @param int $num the number
     * @return Canvas
     */
    public function getCanvasNumber($num = 0)
    {
        return $this->getDefaultSequence()->get($num);
    }

    /**
     * Given a the number of a sequence for this manifest, retrieve all of its canvases
     * @param int $fromSequence number of the sequence
     * @return array<Canvas>
     */
    public function getCanvases($fromSequence = 0)
    {
        return $this->getSequence($fromSequence)->getCanvases();
    }

    /**
     * Given the number of a sequence for this manifest, retrieve all thumbnail URLs for its canvases
     * @param int $sequenceNum number of the sequence
     * @return array<string>
     */
    public function getThumbnails($sequenceNum = 0)
    {
        $canvases = $this->getSequence($sequenceNum);

        return $canvases->map(function (Canvas $canvas) {
            return $canvas->getThumbnail();
        });
    }

    /**
     * Retrieve the default (i. e. first) sequence for this manifest
     * @return Sequence
     */
    public function getDefaultSequence(): Sequence
    {
        return $this->sequences[0];
    }

    /**
     * Retrieve a sequence using its index for this manifest
     * @param int $num the number
     * @return Sequence|null
     */
    public function getSequence($num)
    {
        return $this->sequences[$num] ?? null;
    }

    /**
     * Given a URI for a canvas and the index for a sequence, retrieve the Canvas object
     * @param string $id URI for the canvas
     * @param int $sequence the index for the sequence
     * @return Canvas
     */
    public function getCanvas(string $id, int $sequence = 0)
    {
        return $this->getSequence($sequence)->find(function (Canvas $canvas) use ($id) {
            return $canvas->getId() === $id;
        });
    }

    /**
     * Given a URI for a canvas and the index for a sequence, determine whether or not this Manifest contains the Canvas
     * @return boolean
     */
    public function containsCanvas(string $id, int $sequence = 0)
    {
        return (bool) $this->getCanvas($id, $sequence);
    }
}
