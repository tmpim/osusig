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
     * How much to round the outer edges of the signature card
     */
    const SIG_OUTER_ROUNDING = 4;

    /**
     * How large the triangle strip should be
     */
    const TRIANGLE_STRIP_HEIGHT = 32;

    /**
     * The triangles image to use for the signature card's header
     */
    const IMG_TRIANGLES = "img/triangles_all.png";


    /**
     * @var string The username of the requested signature's subject
     */
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
     * Calculates the required width of the signature based on the components
     * and their layout.
     *
     * @return int The width we calculated.
     */
    private function calculateBaseWidth() {
        // do something with components
        return 330;
    }

    /**
     * Calculates the required height of the signature based on the components
     * and their layout.
     *
     * @return int The height we calculated.
     */
    private function calculateBaseHeight() {
        // do something with components
        return 86;
    }

    /**
     * Draws the background colour with triangles for the signature
     *
     * @param string $hexColour [Hexadecimal colour value for the whole card]
     */
    public function drawBackground($hexColour) {
        $width = $this->canvas->getImageWidth();
        $height = $this->canvas->getImageHeight();

        /*
            The inner width and height of the signature card.
            This excludes the margins.
        */
        $baseWidth = $this->calculateBaseWidth();
        $baseHeight = $this->calculateBaseHeight();

        $base = new Imagick();
        $base->newImage($width, $height, new ImagickPixel('transparent'));

        // The background layer - this draws the fill
        $background = new ImagickDraw();
        $background->setFillColor($hexColour);
        $background->roundRectangle(
            self::SIG_MARGIN + (self::SIG_STROKE_WIDTH / 2),
            self::SIG_MARGIN + (self::SIG_STROKE_WIDTH / 2),
            ($baseWidth + self::SIG_MARGIN - 1) - (self::SIG_STROKE_WIDTH / 2),
            ($baseHeight + self::SIG_MARGIN - 1) - (self::SIG_STROKE_WIDTH / 2),
            self::SIG_OUTER_ROUNDING,
            self::SIG_OUTER_ROUNDING);
        $base->drawImage($background);

        // The triangles layer itself
        $triangles = new Imagick();
        $triangles->newImage(
            $baseWidth - (self::SIG_STROKE_WIDTH * 2),
            self::TRIANGLE_STRIP_HEIGHT  - self::SIG_STROKE_WIDTH,
            new ImagickPixel($hexColour));
        $triangles = $triangles->textureImage(new Imagick(self::IMG_TRIANGLES));
        $triangles->setImageOpacity(0.125);

        // The gradient to draw over the triangles
        $trianglesGradient = new Imagick();
        $trianglesGradient->newPseudoImage(
            $baseWidth - (self::SIG_STROKE_WIDTH * 2),
            self::TRIANGLE_STRIP_HEIGHT - self::SIG_STROKE_WIDTH,
            'gradient:' . 'none' . '-' . $hexColour);

        // Composite the triangles onto the base
        $base->compositeImage(
            $triangles,
            Imagick::COMPOSITE_DEFAULT,
            self::SIG_MARGIN + self::SIG_STROKE_WIDTH,
            self::SIG_MARGIN + self::SIG_STROKE_WIDTH);

        // Composite the triangles gradient onto the base
        $base->compositeImage($trianglesGradient,
            Imagick::COMPOSITE_DEFAULT,
            self::SIG_MARGIN + self::SIG_STROKE_WIDTH,
            self::SIG_MARGIN + self::SIG_STROKE_WIDTH);

        // Composite the base onto the canvas at margin, margin
        $this->canvas->compositeImage($base, Imagick::COMPOSITE_DEFAULT, 0, 0);
    }

    /**
     * Draws the white area of the card
     */
    public function drawPlainArea() {
        /*
            The inner width and height of the signature card.
            This excludes the margins.
        */
        $baseWidth = $this->calculateBaseWidth();
        $baseHeight = $this->calculateBaseHeight();

        $plainArea = new ImagickDraw();
        $plainArea->setFillColor("white");
        $plainArea->rectangle(
            self::SIG_MARGIN + self::SIG_STROKE_WIDTH,
            self::SIG_MARGIN + self::SIG_STROKE_WIDTH + self::TRIANGLE_STRIP_HEIGHT,
            $baseWidth - self::SIG_STROKE_WIDTH + (self::SIG_STROKE_WIDTH / 2) + 1,
            $baseHeight - self::SIG_STROKE_WIDTH + (self::SIG_STROKE_WIDTH / 2) + 1
        );

        $this->canvas->drawImage($plainArea);
    }

    /**
     * Draws the stroke over the whole card
     *
     * @param string $hexColour [Hexadecimal colour value for the card stroke]
     */
    public function drawFinalStroke($hexColour) {
        /*
            The inner width and height of the signature card.
            This excludes the margins.
        */
        $baseWidth = $this->calculateBaseWidth();
        $baseHeight = $this->calculateBaseHeight();

        $cardStroke = new ImagickDraw();
        $cardStroke->setStrokeColor($hexColour);
        $cardStroke->setStrokeWidth(3.0);
        $cardStroke->setFillColor(new ImagickPixel('transparent'));
        $cardStroke->roundRectangle(
            self::SIG_MARGIN + (self::SIG_STROKE_WIDTH / 2),
            self::SIG_MARGIN + (self::SIG_STROKE_WIDTH / 2),
            ($baseWidth + self::SIG_MARGIN - 1) - (self::SIG_STROKE_WIDTH / 2),
            ($baseHeight + self::SIG_MARGIN - 1) - (self::SIG_STROKE_WIDTH / 2),
            self::SIG_OUTER_ROUNDING,
            self::SIG_OUTER_ROUNDING);

        $this->canvas->drawImage($cardStroke);
    }

    public function generate($hexColour = "#bb1177") {
        $this->drawBackground($hexColour);
        $this->drawPlainArea();
        $this->drawFinalStroke($hexColour);

        // Sets the headers and echoes the image
        $this->output();
    }
}