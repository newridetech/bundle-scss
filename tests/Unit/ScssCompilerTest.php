<?php

namespace Newride\Scss\Tests\Unit;

use Newride\Scss\ScssCompiler;
use Newride\Scss\Tests\TestCase;

class ScssCompilerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->compiler = app(ScssCompiler::class);
    }

    public function testBasicTest()
    {
        $this->assertSame(
            'body a{color:red}',
            $this->compiler->resource('assets/fixture.scss')
        );
    }

    /**
     * @expectedException \Newride\Scss\Exception\FileNotFound
     */
    public function testNonExistentFile()
    {
        $this->compiler->resource('assets/non-existent.scss');
    }
}
