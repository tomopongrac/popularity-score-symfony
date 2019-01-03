<?php

namespace App\Responses;

class JsonResponseV1 implements ResponseInterface
{

    public function tranformValidationData($data): array
    {
        return [];
    }

    public function transformNormalData($data): array
    {
        return [
            'term' => $data['term'],
            'score' => $data['score'],
        ];
    }

    public function getResponseHeader(): array
    {
        return [];
    }
}