<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface BitwiseOpcode
{
    /**
     * @var int
     */
    const ORB_IMM8_TO_R0 = 0x10;

    /**
     * @var int
     */
    const ORB_IMM8_TO_R1 = 0x11;

    /**
     * @var int
     */
    const ORB_IMM8_TO_R2 = 0x12;

    /**
     * @var int
     */
    const ORB_IMM8_TO_R3 = 0x13;

    /**
     * @var int
     */
    const ORB_R0_TO_R0 = 0x14;

    /**
     * @var int
     */
    const ORB_R1_TO_R0 = 0x15;

    /**
     * @var int
     */
    const ORB_R2_TO_R0 = 0x16;

    /**
     * @var int
     */
    const ORB_R3_TO_R0 = 0x17;

    /**
     * @var int
     */
    const ORB_R0_TO_R1 = 0x18;

    /**
     * @var int
     */
    const ORB_R1_TO_R1 = 0x19;

    /**
     * @var int
     */
    const ORB_R2_TO_R1 = 0x1a;

    /**
     * @var int
     */
    const ORB_R3_TO_R1 = 0x1b;

    /**
     * @var int
     */
    const ORB_R0_TO_R2 = 0x1c;

    /**
     * @var int
     */
    const ORB_R1_TO_R2 = 0x1d;

    /**
     * @var int
     */
    const ORB_R2_TO_R2 = 0x1e;

    /**
     * @var int
     */
    const ORB_R3_TO_R2 = 0x1f;

    /**
     * @var int
     */
    const ORB_R0_TO_R3 = 0x20;

    /**
     * @var int
     */
    const ORB_R1_TO_R3 = 0x21;

    /**
     * @var int
     */
    const ORB_R2_TO_R3 = 0x22;

    /**
     * @var int
     */
    const ORB_R3_TO_R3 = 0x23;
}
