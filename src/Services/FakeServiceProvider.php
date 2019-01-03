<?php

namespace App\Services;

class FakeServiceProvider extends ServiceProvider implements ServiceProviderInterface
{
    public function getResult(string $searchTerm)
    {
        if (strpos($searchTerm, self::POSITIVE_WORD_SULFIX) !== false) {
            return json_encode([
                "total_count" => 333,
            ]);
        }

        return json_encode([
            "total_count" => 667
        ]);
    }

    public function getCount($result): int
    {
        return json_decode($result)->total_count;
    }
}