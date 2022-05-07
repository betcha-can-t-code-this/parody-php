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

    /**
     * @var int
     */
    const XORB_IMM8_TO_R0 = 0x24;

    /**
     * @var int
     */
    const XORB_IMM8_TO_R1 = 0x25;

    /**
     * @var int
     */
    const XORB_IMM8_TO_R2 = 0x26;

    /**
     * @var int
     */
    const XORB_IMM8_TO_R3 = 0x27;

    /**
     * @var int
     */
    const XORB_R0_TO_R0 = 0x28;

    /**
     * @var int
     */
    const XORB_R1_TO_R0 = 0x29;

    /**
     * @var int
     */
    const XORB_R2_TO_R0 = 0x2a;

    /**
     * @var int
     */
    const XORB_R3_TO_R0 = 0x2b;

    /**
     * @var int
     */
    const XORB_R0_TO_R1 = 0x2c;

    /**
     * @var int
     */
    const XORB_R1_TO_R1 = 0x2d;

    /**
     * @var int
     */
    const XORB_R2_TO_R1 = 0x2e;

    /**
     * @var int
     */
    const XORB_R3_TO_R1 = 0x2f;

    /**
     * @var int
     */
    const XORB_R0_TO_R2 = 0x30;

    /**
     * @var int
     */
    const XORB_R1_TO_R2 = 0x31;

    /**
     * @var int
     */
    const XORB_R2_TO_R2 = 0x32;

    /**
     * @var int
     */
    const XORB_R3_TO_R2 = 0x33;

    /**
     * @var int
     */
    const XORB_R0_TO_R3 = 0x34;

    /**
     * @var int
     */
    const XORB_R1_TO_R3 = 0x35;

    /**
     * @var int
     */
    const XORB_R2_TO_R3 = 0x36;

    /**
     * @var int
     */
    const XORB_R3_TO_R3 = 0x37;
}
