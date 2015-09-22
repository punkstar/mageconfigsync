<?php

namespace MageConfigSync;

use MageConfigSync\Command\DiffCommand;
use MageConfigSync\Command\DumpCommand;
use MageConfigSync\Command\LoadCommand;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct("mageconfigsync", "0.4.0");

        $this->add(new DumpCommand());
        $this->add(new DiffCommand());
        $this->add(new LoadCommand());
    }
}
