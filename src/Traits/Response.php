<?php

namespace Opsource\QueryAdapter\Traits;

use Psr\Http\Message\ResponseInterface;

trait Response
{
    protected ResponseInterface $response;
    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

}
