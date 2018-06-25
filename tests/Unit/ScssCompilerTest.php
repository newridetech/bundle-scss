<?php

namespace Newride\Scss\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Newride\Scss\ScssCompiler;
use Newride\Scss\Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $compiler = app(ScssCompiler::class);

        $this->assertSame(
            'body a{color:red}',
            $compiler->resource('assets/fixture.scss')
        );
    }
}
