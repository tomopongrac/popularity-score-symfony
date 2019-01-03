<?php

namespace App\Responses;

use App\Exception\JsonResponseNotExistException;

class JsonResponseFactory
{
    public static function create($version): ResponseInterface
    {
        $version = strtoupper($version);
        $className = '\App\Responses\JsonResponse' . $version;

        if (!class_exists($className)) {
            throw new JsonResponseNotExistException();
        }

        return new $className;
    }
}