<?php

namespace App\Responses;

interface ResponseInterface
{
    public function tranformValidationData($data): array;

    public function transformNormalData($data): array;

    public function getResponseHeader(): array;
}