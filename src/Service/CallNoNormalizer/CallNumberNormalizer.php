<?php

namespace App\Service\CallNoNormalizer;

interface CallNumberNormalizer
{
    public function normalize(string $call_number): string;
}