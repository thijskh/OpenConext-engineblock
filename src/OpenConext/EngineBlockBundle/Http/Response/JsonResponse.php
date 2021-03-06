<?php

namespace OpenConext\EngineBlockBundle\Http\Response;

use OpenConext\EngineBlockBundle\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class JsonResponse extends Response
{
    public function __construct($content, $status = 200, array $headers = [])
    {
        if (!is_string($content)) {
            throw InvalidArgumentException::invalidType('string', 'content', $content);
        }

        parent::__construct($content, $status, $headers);

        $this->headers->set('Content-Type', 'application/json');
    }
}
