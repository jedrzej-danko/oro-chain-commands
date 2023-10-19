<?php

namespace App;

class ChainCommandConfig
{
    private array $chains = [
        'app:first-cli' => ['app:second-cli', 'app:test-cli'],
        'app:second-cli' => ['app:test-cli']
    ];

    public function getChains(): array
    {
        return $this->chains;
    }
}