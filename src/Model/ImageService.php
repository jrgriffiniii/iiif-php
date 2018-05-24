<?php

namespace IIIF\Model;

/**
 * Class modeling image services within the <a href ="http://iiif.io/api/image/2.1/#technical-properties">IIIF Image API</a>
 */
class ImageService
{
    use WithMetaData; /**< Mixes in the WithMetaData Trait */
    private $tiles; /**< @var array<Tile> tiles generated for the image content being requested */
    private $id; /**< @var string the URI for the Image Service */
    private $height; /**< @var int the height (in pixels) for the image content requested from the service */
    private $width; /**< @var int the width (in pixels) for the image content requested from the service */

    /**
     * Constructor
     * @param string $id URI for the image service
     * @param int $height height for the image requested from the service
     * @param int $width width for the image requested from the service
     * @param array<Tile> $tiles tiles generated for the requested image
     */
    public function __construct(
        string $id,
        int $height,
        int $width,
        array $tiles = null
    ) {
        $this->tiles = $tiles ? $tiles : [];
        $this->id = $id;
        $this->height = $height;
        $this->width = $width;
    }

    /**
     * Construct an object from an array of values
     * @param array $resource array of values being used to construct an ImageService
     * @return ImageService
     */
    public static function fromArray($service)
    {
        return new static(
            $service['@id'],
            $service['height'],
            $service['width'],
            isset($service['tiles']) ? array_map(function ($tile) : Tile {
                return Tile::fromArray($tile);
            }, $service['tiles']) : null
        );
    }

    /**
     * Accessor method for the image service URI
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns a Tile object for a specific numeric index
     * @param int $num the number of the tile in the array
     * @return Tile
     */
    public function getTile(int $num)
    {
        return $this->tiles[$num];
    }

    /**
     * Returns the Tile object with the largest dimensions in the array
     * (This is used in order to generate the URI for the thumbnail)
     * @see getThumbnail()
     * @return Tile
     */
    public function getLargestTile()
    {
        $largest = 0;
        foreach ($this->tiles as $tile) {
            /** @var $tile Tile */
            if ($tile->getLargestDimension() > $largest) {
                $largest = $tile->getLargestDimension();
            }
        }

        return $largest === 0 ? 256 : $largest;
    }

    /**
     * Generates the URI for the thumbnail used for an image requested from the service
     * (Defaults to the tile with the largest dimensions)
     * @see getLargestTile()
     * @return string
     */
    public function getThumbnail()
    {
        $largestTile = $this->getLargestTile();

        return $this->id.'/full/'.$largestTile.',/0/default.jpg';
    }

    /**
     * Generates the URI for a given region in an image available in this service
     * @param Region $region object defining the rectangular region specified in the request
     * @return string
     */
    public function getRegion(Region $region)
    {
        $largestTile = $this->getLargestTile();

        return $this->id.'/'.$region->getX().','.$region->getY().','.$region->getWidth().','.$region->getHeight().'/'.$largestTile.',/0/default.jpg';
    }
}
