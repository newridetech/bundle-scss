<?php

namespace Newride\Scss;

class RouteFilename
{
    protected $routeName;

    public function __construct(string $routeName)
    {
        $this->routeName = $routeName;
    }

    public function __toString(): string
    {
        return config('scss.resources.path').'/'.str_replace('.', '-', $this->routeName).'.scss';
    }
}
