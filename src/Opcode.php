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
    const MULB_R0_TO_R0 = 0x40;

    /**
     * @var int
     */
    const MULB_R1_TO_R0 = 0x41;

    /**
     * @var int
     */
    const MULB_R2_TO_R0 = 0x42;

    /**
     * @var int
     */
    const MULB_R3_TO_R0 = 0x43;

    /**
     * @var int
     */
    const MULB_R0_TO_R1 = 0x44;

    /**
     * @var int
     */
    const MULB_R1_TO_R1 = 0x45;

    /**
     * @var int
     */
    const MULB_R2_TO_R1 = 0x46;

    /**
     * @var int
     */
    const MULB_R3_TO_R1 = 0x47;

    /**
     * @var int
     */
    const MULB_R0_TO_R2 = 0x48;

    /**
     * @var int
     */
    const MULB_R1_TO_R2 = 0x49;

    /**
     * @var int
     */
    const MULB_R2_TO_R2 = 0x4a;

    /**
     * @var int
     */
    const MULB_R3_TO_R2 = 0x4b;

    /**
     * @var int
     */
    const MULB_R0_TO_R3 = 0x4c;

    /**
     * @var int
     */
    const MULB_R1_TO_R3 = 0x4d;

    /**
     * @var int
     */
    const MULB_R2_TO_R3 = 0x4e;

    /**
     * @var int
     */
    const MULB_R3_TO_R3 = 0x4f;

    /**
     * @var int
     */
    const DIVB_R0_TO_R0 = 0x50;

    /**
     * @var int
     */
    const DIVB_R1_TO_R0 = 0x51;

    /**
     * @var int
     */
    const DIVB_R2_TO_R0 = 0x52;

    /**
     * @var int
     */
    const DIVB_R3_TO_R0 = 0x53;

    /**
     * @var int
     */
    const DIVB_R0_TO_R1 = 0x54;

    /**
     * @var int
     */
    const DIVB_R1_TO_R1 = 0x55;

    /**
     * @var int
     */
    const DIVB_R2_TO_R1 = 0x56;

    /**
     * @var int
     */
    const DIVB_R3_TO_R1 = 0x57;

    /**
     * @var int
     */
    const DIVB_R0_TO_R2 = 0x58;

    /**
     * @var int
     */
    const DIVB_R1_TO_R2 = 0x59;

    /**
     * @var int
     */
    const DIVB_R2_TO_R2 = 0x5a;

    /**
     * @var int
     */
    const DIVB_R3_TO_R2 = 0x5b;

    /**
     * @var int
     */
    const DIVB_R0_TO_R3 = 0x5c;

    /**
     * @var int
     */
    const DIVB_R1_TO_R3 = 0x5d;

    /**
     * @var int
     */
    const DIVB_R2_TO_R3 = 0x5e;

    /**
     * @var int
     */
    const DIVB_R3_TO_R3 = 0x5f;

    /**
     * @var int
     */
    const MOVB_IMM8_TO_R0 = 0x60;

    /**
     * @var int
     */
    const MOVB_IMM8_TO_R1 = 0x61;

    /**
     * @var int
     */
    const MOVB_IMM8_TO_R2 = 0x62;

    /**
     * @var int
     */
    const MOVB_IMM8_TO_R3 = 0x63;

    /**
     * @var int
     */
    const ADDB_IMM8_TO_R0 = 0x64;

    /**
     * @var int
     */
    const ADDB_IMM8_TO_R1 = 0x65;

    /**
     * @var int
     */
    const ADDB_IMM8_TO_R2 = 0x66;

    /**
     * @var int
     */
    const ADDB_IMM8_TO_R3 = 0x67;

    /**
     * @var int
     */
    const SUBB_IMM8_TO_R0 = 0x68;

    /**
     * @var int
     */
    const SUBB_IMM8_TO_R1 = 0x69;

    /**
     * @var int
     */
    const SUBB_IMM8_TO_R2 = 0x6a;

    /**
     * @var int
     */
    const SUBB_IMM8_TO_R3 = 0x6b;

    /**
     * @var int
     */
    const MULB_IMM8_TO_R0 = 0x6c;

    /**
     * @var int
     */
    const MULB_IMM8_TO_R1 = 0x6d;

    /**
     * @var int
     */
    const MULB_IMM8_TO_R2 = 0x6e;

    /**
     * @var int
     */
    const MULB_IMM8_TO_R3 = 0x6f;

    /**
     * @var int
     */
    const DIVB_IMM8_TO_R0 = 0x70;

    /**
     * @var int
     */
    const DIVB_IMM8_TO_R1 = 0x71;

    /**
     * @var int
     */
    const DIVB_IMM8_TO_R2 = 0x72;

    /**
     * @var int
     */
    const DIVB_IMM8_TO_R3 = 0x73;

    /**
     * @var int
     */
    const CMPB_R0_TO_R0 = 0x74;

    /**
     * @var int
     */
    const CMPB_R1_TO_R0 = 0x75;

    /**
     * @var int
     */
    const CMPB_R2_TO_R0 = 0x76;

    /**
     * @var int
     */
    const CMPB_R3_TO_R0 = 0x77;

    /**
     * @var int
     */
    const CMPB_R0_TO_R1 = 0x78;

    /**
     * @var int
     */
    const CMPB_R1_TO_R1 = 0x79;

    /**
     * @var int
     */
    const CMPB_R2_TO_R1 = 0x7a;

    /**
     * @var int
     */
    const CMPB_R3_TO_R1 = 0x7b;

    /**
     * @var int
     */
    const CMPB_R0_TO_R2 = 0x7c;

    /**
     * @var int
     */
    const CMPB_R1_TO_R2 = 0x7d;

    /**
     * @var int
     */
    const CMPB_R2_TO_R2 = 0x7e;

    /**
     * @var int
     */
    const CMPB_R3_TO_R2 = 0x7f;

    /**
     * @var int
     */
    const CMPB_R0_TO_R3 = 0x80;

    /**
     * @var int
     */
    const CMPB_R1_TO_R3 = 0x81;

    /**
     * @var int
     */
    const CMPB_R2_TO_R3 = 0x82;

    /**
     * @var int
     */
    const CMPB_R3_TO_R3 = 0x83;

    /**
     * @var int
     */
    const CMPB_IMM8_TO_R0 = 0x84;

    /**
     * @var int
     */
    const CMPB_IMM8_TO_R1 = 0x85;

    /**
     * @var int
     */
    const CMPB_IMM8_TO_R2 = 0x86;

    /**
     * @var int
     */
    const CMPB_IMM8_TO_R3 = 0x87;

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
    const PRIB_IMM8 = 0xb4;

    /**
     * @var int
     */
    const INCB_R0 = 0xb5;

    /**
     * @var int
     */
    const INCB_R1 = 0xb6;

    /**
     * @var int
     */
    const INCB_R2 = 0xb7;

    /**
     * @var int
     */
    const INCB_R3 = 0xb8;

    /**
     * @var int
     */
    const DECB_R0 = 0xb9;

    /**
     * @var int
     */
    const DECB_R1 = 0xba;

    /**
     * @var int
     */
    const DECB_R2 = 0xbb;

    /**
     * @var int
     */
    const DECB_R3 = 0xbc;

    /**
     * @var int
     */
    const JUMP_REX_PREFIX = 0xc0;

    /**
     * @var int
     */
    const BITWISE_REX_CALL_GATE_PREFIX = 0xc1;

    /**
     * @var int
     */
    const HALT = 0xfd;
}
