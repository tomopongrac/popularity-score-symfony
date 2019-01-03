<?php

namespace App\Services;

abstract class ServiceProvider
{
    const POSITIVE_WORD_SULFIX = 'rocks';
    const NEGATIVE_WORD_SULFIX = 'sucks';

    public abstract function getResult(string $searchTerm);

    public abstract function getCount($result): int;

    public function getScore(string $word): float
    {
        $positiveCount = $this->getCount($this->getResult($word . ' ' . self::POSITIVE_WORD_SULFIX));
        $negativeCount = $this->getCount($this->getResult($word . ' ' . self::NEGATIVE_WORD_SULFIX));

        return $this->calculateScore($positiveCount, $negativeCount);
    }

    public function calculateScore(int $positiveCount, int $negativeCount): float
    {
        if ($positiveCount == 0 && $negativeCount === 0) {
            return 0;
        }

        return number_format($positiveCount / ($positiveCount + $negativeCount) * 10, 2);
    }
}