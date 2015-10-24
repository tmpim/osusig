<?php
/**
 * @author Lemmy
 */
class Signature
{
    protected $canvas;

    /**
     * Creates a new instance of a basic Signature class
     *
     * @param int $width The signature's canvas width
     * @param int $height The signature's canvas height
     */
    public function __construct($width, $height) {
        $this->canvas = new\Imagick();
        $this->canvas->newImage($width, $height, new ImagickPixel('transparent'));
    }

    /**
     * Draws the background colour for the signature
     *
     * @param string $hexColour Hexadecimal colour value
     */
    protected function drawBackground($hexColour) {
        $background = new ImagickDraw();
        $background->setFillColor($hexColour);
        $background->rectangle(0, 0, $this->canvas->getImageWidth(), $this->canvas->getImageHeight());

        $this->canvas->drawImage($background);
    }

    /**
     * Renders the signature to the browser
     */
    public function output() {
        $this->canvas->setImageFormat('png');

        header('Content-Type: image/'.$this->canvas->getImageFormat());

        header("Cache-Control: max-age=1800");
        header("Expires: ".gmdate("D, d M Y H:i:s", time()+1800)." GMT");

        echo $this->canvas;
    }

    /**
     * @return Imagick The Imagick object of this signature.
     */
    public function getCanvas() {
        return $this->canvas;
    }
}