<?php

namespace App\Responses;

class JsonResponseV2 implements ResponseInterface
{

    public function tranformValidationData($data): array
    {
        return [
            'error' => [
                'message' => 'Riječ mora biti upisana.'
            ]
        ];
    }

    public function transformNormalData($data): array
    {
        return [
            'data' => [
                'term' => $data['term'],
                'score' => $data['score'],
            ],
        ];
    }

    public function getResponseHeader(): array
    {
        return [
            'Accept' => 'application/vnd.api+json',
        ];
    }
}