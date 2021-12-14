<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface Opcode
{
    /**
     * @var int
     */
    const MOVB_R0_TO_R0 = 0x10;

    /**
     * @var int
     */
    const MOVB_R1_TO_R0 = 0x11;

    /**
     * @var int
     */
    const MOVB_R2_TO_R0 = 0x12;

    /**
     * @var int
     */
    const MOVB_R3_TO_R0 = 0x13;

    /**
     * @var int
     */
    const MOVB_R0_TO_R1 = 0x14;

    /**
     * @var int
     */
    const MOVB_R1_TO_R1 = 0x15;

    /**
     * @var int
     */
    const MOVB_R2_TO_R1 = 0x16;

    /**
     * @var int
     */
    const MOVB_R3_TO_R1 = 0x17;

    /**
     * @var int
     */
    const MOVB_R0_TO_R2 = 0x18;

    /**
     * @var int
     */
    const MOVB_R1_TO_R2 = 0x19;

    /**
     * @var int
     */
    const MOVB_R2_TO_R2 = 0x1a;

    /**
     * @var int
     */
    const MOVB_R3_TO_R2 = 0x1b;

    /**
     * @var int
     */
    const MOVB_R0_TO_R3 = 0x1c;

    /**
     * @var int
     */
    const MOVB_R1_TO_R3 = 0x1d;

    /**
     * @var int
     */
    const MOVB_R2_TO_R3 = 0x1e;

    /**
     * @var int
     */
    const MOVB_R3_TO_R3 = 0x1f;

    /**
     * @var int
     */
    const ADDB_R0_TO_R0 = 0x20;

    /**
     * @var int
     */
    const ADDB_R1_TO_R0 = 0x21;

    /**
     * @var int
     */
    const ADDB_R2_TO_R0 = 0x22;

    /**
     * @var int
     */
    const ADDB_R3_TO_R0 = 0x23;

    /**
     * @var int
     */
    const ADDB_R0_TO_R1 = 0x24;

    /**
     * @var int
     */
    const ADDB_R1_TO_R1 = 0x25;

    /**
     * @var int
     */
    const ADDB_R2_TO_R1 = 0x26;

    /**
     * @var int
     */
    const ADDB_R3_TO_R1 = 0x27;

    /**
     * @var int
     */
    const ADDB_R0_TO_R2 = 0x28;

    /**
     * @var int
     */
    const ADDB_R1_TO_R2 = 0x29;

    /**
     * @var int
     */
    const ADDB_R2_TO_R2 = 0x2a;

    /**
     * @var int
     */
    const ADDB_R3_TO_R2 = 0x2b;

    /**
     * @var int
     */
    const ADDB_R0_TO_R3 = 0x2c;

    /**
     * @var int
     */
    const ADDB_R1_TO_R3 = 0x2d;

    /**
     * @var int
     */
    const ADDB_R2_TO_R3 = 0x2e;

    /**
     * @var int
     */
    const ADDB_R3_TO_R3 = 0x2f;

    /**
     * @var int
     */
    const SUBB_R0_TO_R0 = 0x30;

    /**
     * @var int
     */
    const SUBB_R1_TO_R0 = 0x31;

    /**
     * @var int
     */
    const SUBB_R2_TO_R0 = 0x32;

    /**
     * @var int
     */
    const SUBB_R3_TO_R0 = 0x33;

    /**
     * @var int
     */
    const SUBB_R0_TO_R1 = 0x34;

    /**
     * @var int
     */
    const SUBB_R1_TO_R1 = 0x35;

    /**
     * @var int
     */
    const SUBB_R2_TO_R1 = 0x36;

    /**
     * @var int
     */
    const SUBB_R3_TO_R1 = 0x37;

    /**
     * @var int
     */
    const SUBB_R0_TO_R2 = 0x38;

    /**
     * @var int
     */
    const SUBB_R1_TO_R2 = 0x39;

    /**
     * @var int
     */
    const SUBB_R2_TO_R2 = 0x3a;

    /**
     * @var int
     */
    const SUBB_R3_TO_R2 = 0x3b;

    /**
     * @var int
     */
    const SUBB_R0_TO_R3 = 0x3c;

    /**
     * @var int
     */
    const SUBB_R1_TO_R3 = 0x3d;

    /**
     * @var int
     */
    const SUBB_R2_TO_R3 = 0x3e;

    /**
     * @var int
     */
    const SUBB_R3_TO_R3 = 0x3f;

    /**
     * @var int
     */
    const MOVB_IMM8_TO_R0 = 0x50;

    /**
     * @var int
     */
    const MOVB_IMM8_TO_R1 = 0x51;

    /**
     * @var int
     */
    const MOVB_IMM8_TO_R2 = 0x52;

    /**
     * @var int
     */
    const MOVB_IMM8_TO_R3 = 0x53;

    /**
     * @var int
     */
    const ADDB_IMM8_TO_R0 = 0x54;

    /**
     * @var int
     */
    const ADDB_IMM8_TO_R1 = 0x55;

    /**
     * @var int
     */
    const ADDB_IMM8_TO_R2 = 0x56;

    /**
     * @var int
     */
    const ADDB_IMM8_TO_R3 = 0x57;

    /**
     * @var int
     */
    const SUBB_IMM8_TO_R0 = 0x58;

    /**
     * @var int
     */
    const SUBB_IMM8_TO_R1 = 0x59;

    /**
     * @var int
     */
    const SUBB_IMM8_TO_R2 = 0x5a;

    /**
     * @var int
     */
    const SUBB_IMM8_TO_R3 = 0x5b;

    /**
     * @var int
     */
    const MULB_IMM8_TO_R0 = 0x5c;

    /**
     * @var int
     */
    const MULB_IMM8_TO_R1 = 0x5d;

    /**
     * @var int
     */
    const MULB_IMM8_TO_R2 = 0x5e;

    /**
     * @var int
     */
    const MULB_IMM8_TO_R3 = 0x5f;

    /**
     * @var int
     */
    const DIVB_IMM8_TO_R0 = 0x60;

    /**
     * @var int
     */
    const DIVB_IMM8_TO_R1 = 0x61;

    /**
     * @var int
     */
    const DIVB_IMM8_TO_R2 = 0x62;

    /**
     * @var int
     */
    const DIVB_IMM8_TO_R3 = 0x63;

    /**
     * @var int
     */
    const PRIB_R0 = 0xb0;

    /**
     * @var int
     */
    const PRIB_R1 = 0xb1;

    /**
     * @var int
     */
    const PRIB_R2 = 0xb2;

    /**
     * @var int
     */
    const PRIB_R3 = 0xb3;

    /**
     * @var int
     */
    const PRIB_IMM8 = 0xbf;
}
