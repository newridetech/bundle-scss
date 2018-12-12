<?php

namespace Newride\Scss;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Compressed;
use Newride\Scss\Exception\FileNotFound;

class ScssCompiler
{
    protected $cache;

    protected $compiler;

    public function __construct(Compiler $compiler)
    {
        $this->cache = new ScssCompilerCache();

        $this->compiler = $compiler;
        $this->compiler->setFormatter(Compressed::class);
        $this->compiler->setImportPaths(resource_path(config('scss.resources.path')));
    }

    public function asset(string $file): string
    {
        return $this->file(public_path($file));
    }

    public function current(Request $request = null): string
    {
        if (is_null($request)) {
            $request = request();
        }

        return $this->route($request->route());
    }

    public function file(string $file, string $fallbackFile = null): string
    {
        $processor = function (string $contents) {
            return $this->compiler->compile($contents);
        };
        try {
            return $this->cache->process($file, $processor);
        } catch (FileNotFound $e) {
            if (!is_null($fallbackFile)) {
                return $this->cache->process($fallbackFile, $processor);
            }

            throw $e;
        }
    }

    public function resource(string $file, string $fallbackResource = null): string
    {
        return $this->file(resource_path($file), resource_path($fallbackResource));
    }

    public function route(Route $route): string
    {
        return $this->routeName($route->getName());
    }

    public function routeName(string $routeName): string
    {
        return $this->resource(new RouteFilename($routeName));
    }

    public function routeNamePatternFallback(array $patterns, string $routeName): string
    {
        try {
            return $this->routeName($routeName);
        } catch (FileNotFound $e) {
            foreach ($patterns as $pattern => $resource) {
                if (fnmatch($pattern, $routeName)) {
                    return $this->resource($resource);
                }
            }

            throw $e;
        }
    }
}
