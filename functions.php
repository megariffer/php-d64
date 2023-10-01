<?php

/**
 * Convert PETSCII characters to HTML unicode character codes
 *
 * @param string $string PETSCII string
 *
 * @link https://style64.org/petscii/
 *
 * @return string
 */
function petscii_to_html(string $string): string
{
    $return = '';
    $chars = str_split($string);
    foreach ($chars as $char) {
        $hex = strtoupper(dechex(ord($char)));
        $return .= "&#xe0$hex;";
    }
    return $return;
}
