<?php

namespace App;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class ChainListener
{
    public function __invoke(ConsoleCommandEvent $event)
    {
        echo "ChainListener invoked\n";
    }
}