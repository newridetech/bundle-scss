<?php

namespace Newride\Scss;

use App;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Compressed;
use Webmozart\Glob\Iterator\GlobIterator;

class ScssCompiler
{
    const RESOURCE_BASE_PATH = 'assets/sass';

    public $compiler;

    public static function cacheKeyCompiled(string $file): string
    {
        return 'scss.compiled.'.$file;
    }

    public static function cacheKeyFilemtime(string $file): string
    {
        return 'scss.filemtime.'.$file;
    }

    public static function filemtimeResources(): int
    {
        $filemtime = 0;
        foreach (new GlobIterator(resource_path(self::RESOURCE_BASE_PATH.'/**/*.scss')) as $file) {
            $filemtime = max($filemtime, filemtime($file));
        }

        return $filemtime;
    }

    public static function routeNameToFileName(string $routeName): string
    {
        return self::RESOURCE_BASE_PATH.'/'.str_replace('.', '-', $routeName).'.scss';
    }

    public function __construct(Compiler $compiler)
    {
        $this->compiler = $compiler;
        $this->compiler->setFormatter(Compressed::class);
        $this->compiler->setImportPaths(resource_path(self::RESOURCE_BASE_PATH));
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
        $filemtime = filemtime($file);
        $oldFilemtime = Cache::get(static::cacheKeyFilemtime($file));

        if (App::environment(['local', 'testing'])) {
            $filemtime = max($filemtime, static::filemtimeResources());
        }

        if (empty($oldFilemtime) || $filemtime > $oldFilemtime) {
            Cache::forget(static::cacheKeyCompiled($file));
        }

        return Cache::rememberForever(static::cacheKeyCompiled($file), function () use ($file, $filemtime) {
            Cache::forever(static::cacheKeyFilemtime($file), $filemtime);

            $scss = file_get_contents($file);

            return $this->compiler->compile($scss);
        });
    }

    public function resource(string $file): string
    {
        return $this->file(resource_path($file));
    }

    public function route(Route $route): string
    {
        $name = static::routeNameToFileName($route->getName());

        return $this->resource($name);
    }
}
