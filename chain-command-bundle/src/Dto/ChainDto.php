<?php

namespace OroChain\ChainCommandBundle\Dto;

readonly class ChainDto
{
    public string $startsWith;
    /** @var string[] */
    public array $chain;

    /**
     * @param string $startsWith
     * @param string[] $chain
     */
    public function __construct(string $startsWith, array $chain)
    {
        $this->startsWith = $startsWith;
        $this->chain = $chain;
    }


}