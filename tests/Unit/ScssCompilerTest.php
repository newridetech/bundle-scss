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
            $this->compiler->resource('assets/sass/fixture.scss')
        );
    }

    public function testMatchesExactPattern()
    {
        $this->assertSame(
            'body a{color:blue}',
            $this->compiler->routeNamePatternFallback([
                '*.blue' => 'assets/sass/fixture.scss',
            ], 'fixture.blue')
        );
    }

    public function testMatchesPattern()
    {
        $this->assertSame(
            'body a{color:red}',
            $this->compiler->routeNamePatternFallback([
                '*.index' => 'assets/sass/fixture.scss',
            ], 'test.index')
        );
    }

    /**
     * @expectedException \Newride\Scss\Exception\FileNotFound
     */
    public function testNonExistentFile()
    {
        $this->compiler->resource('assets/sass/non-existent.scss');
    }

    /**
     * @depends testBasicTest
     * @depends testNonExistentFile
     */
    public function testUsesFallbackResource()
    {
        $this->assertSame(
            'body a{color:red}',
            $this->compiler->resource(
                'assets/sass/non-existent.scss',
                'assets/sass/fixture.scss'
            )
        );
    }
}
