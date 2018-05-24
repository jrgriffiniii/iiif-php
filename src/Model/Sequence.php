<?php

namespace IIIF\Model;

/**
 * Class modeling sequences contained within manifests for image resources within the <a href ="http://iiif.io/api/presentation/2.1/#sequence">IIIF Presentation API</a>
 */
class Sequence
{
    use WithMetaData; /**< Mixes in the WithMetaData Trait */

    private $label; /**< @var string label provided for this Sequence */
    private $canvases; /**< $var array<Canvas> Canvases contained within this Sequence */

    /**
     * Constructor
     * @param string $label label provided for this Sequence
     * @param array<Canvas> $canvases Canvases contained within this Sequence
     */
    public function __construct(string $label, array $canvases)
    {
        $this->canvases = $canvases;
        $this->label = $label;
    }

    /**
     * Construct an object from an array of values
     * @param array $sequence array of values being used to construct a Sequence
     * @return Sequence
     */
    public static function fromArray(array $sequence)
    {
        $canvases = array_map(function ($canvas) {
            return Canvas::fromArray($canvas);
        }, $sequence['canvases']);

        return new static(
            $sequence['label'] ?? '',
            $canvases
        );
    }

    /**
     * Accessor method for the member Canvases
     * @return array<Canvas>
     */
    public function getCanvases()
    {
        return $this->canvases;
    }

    /**
     * Given an index for a Canvas, retrieve it for this Sequence
     * @param int $num index for the Canvas
     * @return Canvas|null
     */
    public function get(int $num) : Canvas
    {
        return $this->canvases[$num] ?? null;
    }

    /**
     * Maps the Canvases for this Sequence to a callback, and returns those values
     * @see array_map()
     * @param callable $fn callback mapping the Canvases of this Sequence to values
     * @return array
     */
    public function map(callable $fn)
    {
        return array_map($fn, $this->canvases);
    }

    /**
     * Retrieves a Canvas using a callback (which provides the conditions for matching the desired Canvas)
     * (Matches only the first Canvas found, returns null if it does not find any matching Canvases)
     * @param callable $search callback (should return a boolean)
     * @return Canvas|null
     */
    public function find(callable $search)
    {
        foreach ($this->canvases as $canvas) {
            if ($search($canvas)) {
                return $canvas;
            }
        }

        return null;
    }
}
