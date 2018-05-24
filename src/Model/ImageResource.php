<?php

namespace IIIF\Model;

/**
 * Class modeling image resources within the <a href ="http://iiif.io/api/presentation/2.1/#resource-type-overview">IIIF Presentation API</a>
 */
class ImageResource
{

    use WithMetaData;  /**< Mixes in the WithMetaData Trait */
    private $service; /**< ImageService object modeling the IIIF image service providing image content */
    private $id; /**< URI for the resource */
    private $type; /**< string for the resource type */
    private $format; /**< string for the image format */
    private $height; /**< int for the image height */
    private $width; /**< int for the image width */

    /**
     * Constructor
     * @param string $id URI for the resource
     * @param string $type resource type
     * @param string $format image format
     * @param int $height image height
     * @param int $width image width
     * @param ImageService $service object modeling the IIIF image service providing image content
     */
    public function __construct(
        string $id,
        string $type = null,
        string $format = null,
        int $height,
        int $width,
        ImageService $service = null
    ) {
        $this->service = $service;
        $this->id = $id;
        $this->type = $type;
        $this->format = $format;
        $this->height = $height;
        $this->width = $width;
    }

    /**
     * Construct an object from an array of values
     * @param array $resource array of values being used to construct an ImageResource
     * @return ImageResource
     */
    public static function fromArray($resource) : self
    {
        $service = $resource['service'];
        $service['width'] = $service['width'] ?? $resource['width'];
        $service['height'] = $service['height'] ?? $resource['height'];

        return new static(
            $resource['@id'],
            $resource['@type'] ?? null,
            $resource['format'] ?? null,
            $resource['height'] ?? 0,
            $resource['width'] ?? 0,
            isset($resource['service']) ? ImageService::fromArray($service) : null
        );
    }

    /**
     * Accessor method for the IIIF image service object
     * @return ImageResource
     */
    public function getService()
    {
        return $this->service;
    }
}
