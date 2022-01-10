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
    const CARRY = 0x1;

    /**
     * @var int
     */
    const PARITY = 0x2;

    /**
     * @var int
     */
    const ADJUST = 0x10;

    /**
     * @var int
     */
    const ZERO = 0x40;

    /**
     * @var int
     */
    const SIGN = 0x80;

    /**
     * @var int
     */
    const TRAP = 0x100;

    /**
     * @var int
     */
    const INTERRUPT_ENABLE = 0x200;

    /**
     * @var int
     */
    const DIRECTION = 0x400;

    /**
     * @var int
     */
    const OVERFLOW = 0x800;

    /**
     * @return void
     */
    public function calculate();

    /**
     * @return int
     */
    public function getFlag(): int;

    /**
     * @param int $flag
     * @return void
     */
    public function setFlag(int $flag);
}
