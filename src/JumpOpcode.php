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
    const JUMP_IF_ZERO = 0x11;

    /**
     * @var int
     */
    const JUMP_IF_NOT_ZERO = 0x12;
}
