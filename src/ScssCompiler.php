<?php

namespace Newride\Scss;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Compressed;

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

    public function file(string $file): string
    {
        return $this->cache->process($file, function (string $contents) {
            return $this->compiler->compile($contents);
        });
    }

    public function resource(string $file): string
    {
        return $this->file(resource_path($file));
    }

    public function route(Route $route): string
    {
        $filename = config('scss.resources.path').'/'.str_replace('.', '-', $routeName).'.scss';

        return $this->resource($filename);
    }
}
