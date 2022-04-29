<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface JumpOpcode
{
    /**
     * @var int
     */
    const JUMP_PLAIN = 0x10;

    /**
     * @var int
     */
    const JUMP_IF_EQUAL = 0x11;

    /**
     * @var int
     */
    const JUMP_IF_NOT_EQUAL = 0x12;

    /**
     * @var int
     */
    const JUMP_IF_ZERO = 0x13;

    /**
     * @var int
     */
    const JUMP_IF_NOT_ZERO = 0x14;

    /**
     * @var int
     */
    const JUMP_IF_GREATER = 0x15;

    /**
     * @var int
     */
    const JUMP_IF_GREATER_OR_EQUAL = 0x16;

    /**
     * @var int
     */
    const JUMP_IF_LESS = 0x17;

    /**
     * @var int
     */
    const JUMP_IF_LESS_OR_EQUAL = 0x18;
}
