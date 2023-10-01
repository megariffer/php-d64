<?php

// Convert PETSCII characters to HTML unicode.
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
