<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FunctionsModel extends Model
{
    //
    // get the best text colour (white or black) for the background hex provided
    static public function getWorB($hexCode) {
        // Convert the hex code to an RGB array.
        $hex = str_replace('#', '', $hexCode);
        $rgb = array_map('hexdec', str_split($hex, 2));
        // Calculate the lightness of the color.
        $lightness = ($rgb[0] + $rgb[1] + $rgb[2]) / 3;
      
        // Return black if the color is light, white if it's dark.
        return $lightness > 127 ? "#000000" : "#ffffff";
    }

    // script to get complement colours.
    static public function getComplement($hex) { // get inverted colour
        $hex = str_replace('#', '', $hex);
        $rgb = array_map('hexdec', str_split($hex, 2));
        $complement = array(255 - $rgb[0], 255 - $rgb[1], 255 - $rgb[2]);
        $complementHex = sprintf("%02x%02x%02x", $complement[0], $complement[1], $complement[2]);
        return '#' . $complementHex;
    }

    // adjust brightness of a hex colour
    static public function adjustBrightness($hexCode, $adjustPercent) {
        $hexCode = ltrim($hexCode, '#');
        if (strlen($hexCode) == 3) {
            $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
        }
        $hexCode = array_map('hexdec', str_split($hexCode, 2));
        foreach ($hexCode as & $color) {
            $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
            $adjustAmount = ceil($adjustableLimit * $adjustPercent);
    
            $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
        }
        return '#' . implode($hexCode);
    }
}
