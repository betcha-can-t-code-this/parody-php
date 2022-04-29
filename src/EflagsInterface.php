<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface EflagsInterface
{
    /**
     * @var int
     */
    const ZERO = 0x10;

    /**
     * @var int
     */
    const GREAT = 0x100;

    /**
     * @var int
     */
    const LESS = 0x1000;

    /**
     * @return int
     */
    public function getFlag(): int;

    /**
     * @param int $flag
     * @return void
     */
    public function setFlag(int $flag);

    /**
     * @return bool
     */
    public function isZero(): bool;

    /**
     * @return bool
     */
    public function isGreat(): bool;

    /**
     * @return bool
     */
    public function isLess(): bool;
}
