<?php

namespace FSchmidDev\LeafletMapBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FSchmidDevLeafletMapBundle extends Bundle
{
    public function getPath(): string
    {
        if ($this->path === null) {
            $this->path = \dirname(__DIR__);
        }
        return $this->path;
    }
}