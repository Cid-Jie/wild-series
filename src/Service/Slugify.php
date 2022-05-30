<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input): string
    {
        $search = array('à', 'é', 'è', 'ç', '!', '-', ' ');
        $replace = array('a', 'e', 'e', 'c','', '', '-');
        $slug = preg_replace('/\s\s+/', '', $input);
        $slug = strtolower(str_replace($search, $replace, $input));

        return $slug;
    }
}