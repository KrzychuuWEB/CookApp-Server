<?php

declare(strict_types=1);

namespace App\Service;

class StringConverterService
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function setUppercase(string $string): string
    {
        return strtoupper($string);
    }

    /**
     * @param string $string
     * @param string $prefix
     *
     * @return string
     */
    public function addPrefix(string $string, string $prefix): string
    {
        if (strpos($string, $prefix) !== false) {
            return $string;
        } else {
            return $prefix . $string;
        }
    }
}
