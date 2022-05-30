<?php

namespace App\Service;

class Slugify
{
    public function generate(string $input): string
    {
        $slug = str_replace(' ', '-', $input);
        return $slug;
    }
}