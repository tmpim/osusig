<?php
/**
 * @author Lemmy
 */
class PredefinedColours
{
    /**
     * The pre-defined colours from the v1 and v2 signature
     * generators, here to preserve backwards compatibility.
     */
    private $predefinedColours = array(
        'red' => '#e33',
        'orange' => '#e83',
        'yellow' => '#fc2',
        'green' => '#ad0',
        'blue' => '#6cf',
        'purple' => '#86e',
        'bpink' => '#f6a',
        'darkblue' => '#25e',
        'pink' => '#b17',
        'black' => '#000'
    );

    /**
     * Gets whether or not a colour is named and predefined
     *
     * @param string $colourName The colour to check
     *
     * @return string Hexadecimal colour code if predefined, $colourName if not.
     */
    public function getPredefinedColour($colourName) {
        $colourName = strtolower($colourName);

        return in_array($this->predefinedColours, $colourName) ?
            $this->predefinedColours[$colourName] :
            $colourName;
    }
}