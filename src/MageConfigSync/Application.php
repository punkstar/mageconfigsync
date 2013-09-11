<?php

namespace MageConfigSync;

use MageConfigSync\Command\DumpCommand;
use MageConfigSync\Command\DiffCommand;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new DumpCommand());
        $this->add(new DiffCommand());
    }
}
