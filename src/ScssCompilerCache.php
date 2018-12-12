<?php

namespace Newride\Scss;

use App;
use Cache;
use Newride\Scss\Exception\FileNotFound;
use Webmozart\Glob\Iterator\GlobIterator;

class ScssCompilerCache
{
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
        foreach (new GlobIterator(resource_path(config('scss.resources.path').'/**/*.scss')) as $file) {
            $filemtime = max($filemtime, filemtime($file));
        }

        return $filemtime;
    }

    public function process(string $file, callable $callback): string
    {
        if (!file_exists($file)) {
            throw new FileNotFound($file);
        }

        $filemtime = filemtime($file);
        $oldFilemtime = Cache::get(static::cacheKeyFilemtime($file));

        if (App::environment(['local', 'testing'])) {
            $filemtime = max($filemtime, static::filemtimeResources());
        }

        if (empty($oldFilemtime) || $filemtime > $oldFilemtime) {
            Cache::forget(static::cacheKeyCompiled($file));
        }

        return Cache::rememberForever(static::cacheKeyCompiled($file), static function () use ($callback, $file, $filemtime) {
            Cache::forever(static::cacheKeyFilemtime($file), $filemtime);

            return $callback(file_get_contents($file));
        });
    }
}
