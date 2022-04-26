<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface SerializedEflagsInterface
{
    /**
     * @var string
     */
    const CARRY = 'CF';

    /**
     * @var string
     */
    const PARITY = 'PF';

    /**
     * @var string
     */
    const ADJUST = 'AF';

    /**
     * @var string
     */
    const ZERO = 'ZF';

    /**
     * @var string
     */
    const SIGN = 'SF';

    /**
     * @var string
     */
    const TRAP = 'TF';

    /**
     * @var string
     */
    const INTERRUPT_ENABLE = 'IF';

    /**
     * @var string
     */
    const DIRECTION = 'DF';

    /**
     * @var string
     */
    const OVERFLOW = 'OF';
}
