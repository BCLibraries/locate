<?php

namespace App\Service;

interface CallNumberNormalizer
{
    public function normalize(string $call_number): string;
}