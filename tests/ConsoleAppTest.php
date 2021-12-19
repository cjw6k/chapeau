<?php

namespace Tests;

use Chapeau\ConsoleApp;
use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;
use League\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chapeau\ConsoleApp
 */
class ConsoleAppTest extends TestCase
{
    /**
     * @test
     * @testWith [true]
     *           [0]
     *           [""]
     *           ["foo"]
     *           [[]]
     *           [-1]
     *
     * @param mixed $pipelineReturn
     */
    public function it_returns_EXIT_SUCCESS_when_the_pipeline_returns_not_false($pipelineReturn): void
    {
        $mockPipeline = $this->createConfiguredMock(Pipeline::class, [
            '__invoke' => $pipelineReturn,
        ]);

        $app = new ConsoleApp($mockPipeline);
        $exitStatus = $app->run();

        $this->assertSame(ConsoleApp::EXIT_SUCCESS, $exitStatus);
    }

    /**
     * @test
     */
    public function it_returns_EXIT_FAILURE_when_the_pipeline_returns_false(): void
    {
        $mockPipeline = $this->createConfiguredMock(Pipeline::class, [
            '__invoke' => false,
        ]);

        $app = new ConsoleApp($mockPipeline);
        $exitStatus = $app->run();

        $this->assertSame(ConsoleApp::EXIT_FAILURE, $exitStatus);
    }
}
