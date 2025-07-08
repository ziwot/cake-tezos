<?php

namespace CakeTezos\Domain;

/**
 * Tez Value Object
 */
readonly class Mutez
{
    /**
     * Initialization
     *
     * @param int $mutez
     */
    public function __construct(private int $mutez) {}

    /**
     * Returns tez
     *
     * @return float
     */
    public function tez(): float
    {
        return $this->mutez / 1000_000;
    }

    /**
     * Returns mutez
     *
     * @return int
     */
    public function mutez(): int
    {
        return $this->mutez;
    }
}
