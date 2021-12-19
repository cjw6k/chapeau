<?php

namespace Chapeau;

use League\Pipeline\Pipeline;
use League\Pipeline\PipelineInterface;

class ConsoleApp
{
    const EXIT_SUCCESS = 0;
    const EXIT_FAILURE = 1;

    private Pipeline $pipeline;

    public function __construct(PipelineInterface $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    public function run()
    {
        return ($this->pipeline)(null) === false
            ? static::EXIT_FAILURE
            : static::EXIT_SUCCESS;
    }
}
