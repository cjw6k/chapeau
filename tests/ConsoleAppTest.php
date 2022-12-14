<?php

namespace Tests;

use Chapeau\ConsoleApp;
use Chapeau\ConsoleAppException;
use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;
use League\CLImate\Exceptions\InvalidArgumentException;
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
     *           [false]
     *           [0]
     *           [""]
     *           ["foo"]
     *           [[]]
     *           [-1]
     *
     * @param mixed $pipelineReturn
     */
    public function it_returns_EXIT_FAILURE_when_the_pipeline_returns_non_null($pipelineReturn): void
    {
        $mockPipeline = $this->createConfiguredMock(Pipeline::class, [
            '__invoke' => $pipelineReturn,
        ]);

        $dummyCli = $this->createStub(CLImate::class);
        $dummyCliArgManager = $this->createStub(Manager::class);
        $dummyCli->arguments = $dummyCliArgManager;

        $app = new ConsoleApp($mockPipeline, $dummyCli);
        $exitStatus = $app->run();

        $this->assertSame(ConsoleApp::EXIT_FAILURE, $exitStatus);
    }

    /**
     * @test
     */
    public function it_returns_EXIT_SUCCESS_when_the_pipeline_returns_null(): void
    {
        $mockPipeline = $this->createConfiguredMock(Pipeline::class, [
            '__invoke' => null,
        ]);

        $dummyCli = $this->createStub(CLImate::class);
        $dummyCliArgManager = $this->createStub(Manager::class);
        $dummyCli->arguments = $dummyCliArgManager;

        $app = new ConsoleApp($mockPipeline, $dummyCli);
        $exitStatus = $app->run();

        $this->assertSame(ConsoleApp::EXIT_SUCCESS, $exitStatus);
    }

    /**
     * @test
     */
    public function it_returns_EXIT_FAILURE_when_the_pipeline_throws_a_ConsoleAppException(): void
    {
        $mockPipeline = $this->createMock(Pipeline::class);
        $mockPipeline->expects($this->once())->method('__invoke')->willThrowException(new ConsoleAppException());

        $dummyCli = $this->createStub(CLImate::class);
        $dummyCliArgManager = $this->createStub(Manager::class);
        $dummyCli->arguments = $dummyCliArgManager;

        $app = new ConsoleApp($mockPipeline, $dummyCli);
        $exitStatus = $app->run();

        $this->assertSame(ConsoleApp::EXIT_FAILURE, $exitStatus);
    }

    /**
     * @test
     */
    public function it_prints_the_exception_when_the_pipeline_throws_a_ConsoleAppException(): void
    {
        $exception = new ConsoleAppException('foo');
        $mockPipeline = $this->createMock(Pipeline::class);
        $mockPipeline->expects($this->once())->method('__invoke')->willThrowException($exception);

        $mockCli = $this->createMock(CLImate::class);
        $dummyCliArgManager = $this->createStub(Manager::class);
        $mockCli->arguments = $dummyCliArgManager;
        $mockCli->expects($this->once())->method('__call')->with('error', [(string)$exception]);

        $app = new ConsoleApp($mockPipeline, $mockCli);
        $app->run();
    }

    /**
     * @test
     */
    public function it_returns_EXIT_FAILURE_when_required_CLImate_arguments_are_missing(): void
    {
        $mockCliArgManager = $this->createMock(Manager::class);
        $mockCliArgManager->expects($this->once())->method('parse')->willThrowException(new InvalidArgumentException());
        $stubCli = $this->createStub(CLImate::class);
        $stubCli->arguments = $mockCliArgManager;

        $dummyPipeline = $this->createStub(Pipeline::class);

        $app = new ConsoleApp($dummyPipeline, $stubCli);
        $exitStatus = $app->run();

        $this->assertSame(ConsoleApp::EXIT_FAILURE, $exitStatus);
    }

    /**
     * @test
     */
    public function it_prints_a_usage_message_if_required_CLImate_arguments_are_missing(): void
    {
        $mockCliArgManager = $this->createMock(Manager::class);
        $mockCliArgManager->expects($this->once())->method('parse')->willThrowException(new InvalidArgumentException());
        $mockCli = $this->createMock(CLImate::class);
        $mockCli->arguments = $mockCliArgManager;
        $mockCli->expects($this->once())->method('usage');

        $dummyPipeline = $this->createStub(Pipeline::class);

        $app = new ConsoleApp($dummyPipeline, $mockCli);
        $app->run();
    }

    /**
     * @test
     */
    public function it_prints_an_error_message_describing_semantics_if_arguments_are_not_good(): void
    {
        $cliErrorMessage = 'foo';
        $mockCliArgManager = $this->createMock(Manager::class);
        $mockCliArgManager->expects($this->once())->method('parse')->willThrowException(
            new InvalidArgumentException($cliErrorMessage)
        );
        $mockCli = $this->createMock(CLImate::class);
        $mockCli->arguments = $mockCliArgManager;
        $mockCli->expects($this->once())->method('__call')->with('error', [PHP_EOL . $cliErrorMessage]);

        $dummyPipeline = $this->createStub(Pipeline::class);

        $app = new ConsoleApp($dummyPipeline, $mockCli);
        $app->run();
    }
}
