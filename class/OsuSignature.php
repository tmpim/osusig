<?php
// Thanks to Cygnix
// Created by Lemmmy

class OsuSignature extends Signature
{
    /**
     * The margin of the entire signature card
     */
    const SIG_MARGIN = 4;

    /**
     * The outer stroke width of the signature card
     */
    const SIG_STROKE_WIDTH = 3;

    /**
     * The inner padding of the signature card
     */
    const SIG_INNER_PADDING = 6;

    /**
     * How much to round the edges of the signature card
     */
    const SIG_ROUNDING = 3;

    /**
     * The triangles image to use for the signature card's header
     */
    const IMG_TRIANGLES = "img/triangles_all.png";

    private $user;

    /**
     * Creates a new osu! signature.
     *
     * @param array $user [The user whom the signature will be the signature's subject]
     * @param int $width [The signature's canvas width]
     * $param int $height [The signature's canvas height]
     */
    public function __construct($user, $width = 338, $height = 94) {
        $this->user = $user;

        parent::__construct($width, $height);
    }

    /**
     * Draws the background colour with triangles for the signature
     *
     * @param string $hexColour [Hexadecimal colour value for the whole card]
     */
    public function drawBackground($hexColour)
    {
        $width = $this->canvas->getImageWidth();
        $height = $this->canvas->getImageHeight();

        $baseWidth = calculateBaseWidth();
        $baseHeight = calculateBaseHeight();

        $base = new Imagick();
        $base->newImage($baseWidth, $baseHeight, new ImagickPixel('transparent'));


    }
}