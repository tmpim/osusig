<?php
/**
 * @author Lemmy
 */
class PredefinedColours
{
    /**
     * The pre-defined colours from the v1 and v2 signature
     * generators, here to preserve backwards compatibility.
     *
     * @var array
     */
    private static $predefinedColours = array(
        'red' => '#ee3333',
        'orange' => '#ee8833',
        'yellow' => '#ffcc22',
        'green' => '#aadd00',
        'blue' => '#66ccff',
        'purple' => '#8866ee',
        'bpink' => '#ff66aa',
        'darkblue' => '#2255ee',
        'pink' => '#bb1177',
        'black' => '#000000'
    );

    /**
     * Gets whether or not a colour is named and predefined
     *
     * @param string $colourName The colour to check
     *
     * @return string Hexadecimal colour code if predefined, $colourName if not.
     */
    public static function getPredefinedColour($colourName) {
        $colourName = strtolower($colourName);

        return in_array($colourName, array_keys(PredefinedColours::$predefinedColours)) ?
            PredefinedColours::$predefinedColours[$colourName] :
            $colourName;
    }
}