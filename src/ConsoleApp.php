<?php

namespace Chapeau;

use League\CLImate\CLImate;
use League\CLImate\Exceptions\InvalidArgumentException;
use League\Pipeline\Pipeline;
use League\Pipeline\PipelineInterface;

class ConsoleApp
{
    const EXIT_SUCCESS = 0;
    const EXIT_FAILURE = 1;

    private Pipeline $pipeline;
    private CLImate $cli;

    public function __construct(PipelineInterface $pipeline, CLImate $cli)
    {
        $this->pipeline = $pipeline;
        $this->cli = $cli;
    }

    public function run()
    {
        if (! $this->parseArgs()) {
            return static::EXIT_FAILURE;
        }

        try {
            return ($this->pipeline)(null) === false
                ? static::EXIT_FAILURE
                : static::EXIT_SUCCESS;
        } catch (ConsoleAppException $exc) {
            return static::EXIT_FAILURE;
        }
    }

    protected function parseArgs(): bool
    {
        try {
            $this->cli->arguments->parse();
        } catch (InvalidArgumentException $exc) {
            return false;
        }

        return true;
    }
}
