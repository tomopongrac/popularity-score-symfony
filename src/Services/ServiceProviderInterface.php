<?php

namespace App\Services;

interface ServiceProviderInterface
{
    public function getResult(string $searchTerm);

    public function getCount($result): int;
}
