<?php

namespace Chapeau;

use League\CLImate\CLImate;
use League\CLImate\Exceptions\InvalidArgumentException;
use League\Pipeline\Pipeline;
use League\Pipeline\PipelineInterface;

class ConsoleApp
{
    public const EXIT_SUCCESS = 0;
    public const EXIT_FAILURE = 1;

    private PipelineInterface $pipeline;
    private CLImate $cli;

    public function __construct(PipelineInterface $pipeline, CLImate $cli)
    {
        $this->pipeline = $pipeline;
        $this->cli = $cli;
    }

    public function run(): int
    {
        if (! $this->parseArgs()) {
            return (int)static::EXIT_FAILURE;
        }

        try {
            return is_null(($this->pipeline)(null))
                ? (int)static::EXIT_SUCCESS
                : (int)static::EXIT_FAILURE;
        } catch (ConsoleAppException $exc) {
            $this->cli->error((string)$exc);
            return (int)static::EXIT_FAILURE;
        }
    }

    protected function parseArgs(): bool
    {
        try {
            $this->cli->arguments->parse();
        } catch (InvalidArgumentException $exc) {
            $this->cli->usage();
            $this->cli->error(PHP_EOL . $exc->getMessage());
            return false;
        }

        return true;
    }
}
