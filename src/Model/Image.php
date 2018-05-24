<?php

namespace IIIF\Model;

/**
 * Class modeling image content for canvases within the <a href ="http://iiif.io/api/presentation/2.1/#canvas">IIIF Presentation API</a>
 */
class Image
{
    use WithMetaData; /**< Mixes in the WithMetaData Trait */

    protected $resource; /**< @var ImageResource object modeling the image resource itself */
    private $id; /**< @var string URI for this image resource */
    private $motivation; /**< @var string encoded motivation (usually sc:painting for images, oa:commenting for annotations) for this image resource */
    private $on; /**< @var string URI for the Canvas referenced by this image (usually for annotion linking) */

    /**
     * Constructor
     * @param string $id URI for this image resource
     * @param string $motivation encoded motivation
     * @param string $on URI for the linked Canvas
     * @param ImageResource $imageService object modeling the image resource
     */
    public function __construct(
        string $id,
        string $motivation,
        string $on,
        ImageResource $imageService
    ) {
        $this->resource = $imageService;
        $this->id = $id;
        $this->motivation = $motivation;
        $this->on = $on;
    }

    /**
     * Construct an object from an array of values
     * @param array $image array of values being used to construct an Image
     * @return Image
     */
    public static function fromArray($image)
    {
        return new static(
            $image['@id'],
            $image['motivation'],
            $image['on'],
            ImageResource::fromArray($image['resource'])
        );
    }

    /**
     * Accessor method for the Image URI
     * @return string
     */
    public function getId(): string
    {
        return $this->id ? $this->id : $this->getImageService()->getId();
    }

    /**
     * Accessor method for the encoded motivation
     * @return string
     */
    public function getMotivation()
    {
        return $this->motivation;
    }

    /**
     * Accessor method for the linked Canvas URI
     * @return string
     */
    public function getOn()
    {
        return $this->on;
    }

    /**
     * Accessor method for the image resource object
     * @return ImageResource
     */
    public function getImageService(): ImageService
    {
        return $this->resource->getService();
    }

    /**
     * Retrieves the URI for the thumbnail for the image resource
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->getImageService()->getThumbnail();
    }
}
