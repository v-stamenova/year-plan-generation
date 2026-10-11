<?php

namespace Framework;

class Controller
{
    protected ResponseFactory $responseFactory;

    public function setResponseFactory(ResponseFactory $responseFactory): void
    {
        $this->responseFactory = $responseFactory;
    }
}