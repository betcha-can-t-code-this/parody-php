<?php

declare(strict_types=1);

namespace Vm;

use Vm\Exception\RuntimeException;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
final class Vm implements RuntimeInterface
{
    /**
     * @var int
     */
    private $ip = 0;

    /**
     * @var int
     */
    private $length;

    /**
     * @var string
     */
    private $buffer;

    /**
     * @var \Vm\RegisterInterface
     */
    private $register;

    /**
     * @var \Vm\EflagsInterface
     */
    private $eflags;

    /**
     * @var bool
     */
    private $halted = false;

    /**
     * @param  \Vm\RegisterInterface $register
     * @param  \Vm\EflagsInterface   $eflags
     * @param  string                $buffer
     * @return static
     */
    public function __construct(
        RegisterInterface $register,
        EflagsInterface $eflags,
        string $buffer
    ) {
        $this->setEflags($eflags);
        $this->setRegister($register);
        $this->setBuffer($buffer);
        $this->setLength(strlen($this->getBuffer()));
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        while (!$this->isHalted()) {
            if ($this->isEOF()) {
                break;
            }

            $this->processOpcode();
            $this->incrementIp();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEflags(): EflagsInterface
    {
        return $this->eflags;
    }

    /**
     * {@inheritdoc}
     */
    public function setEflags(EflagsInterface $eflags)
    {
        $this->eflags = $eflags;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegister(): RegisterInterface
    {
        return $this->register;
    }

    /**
     * {@inheritdoc}
     */
    public function setRegister(RegisterInterface $register)
    {
        $this->register = $register;
    }

    /**
     * @return void
     */
    private function halt()
    {
        $this->halted = true;
    }

    /**
     * @return bool
     */
    private function isHalted(): bool
    {
        return $this->halted;
    }

    /**
     * @return bool
     */
    private function isEOF(): bool
    {
        return $this->getIp() >= $this->getLength();
    }

    /**
     * @return int
     */
    private function getLength(): int
    {
        return $this->length;
    }

    /**
     * @param  int $length
     * @return void
     */
    private function setLength(int $length)
    {
        $this->length = $length;
    }

    /**
     * @return string
     */
    private function getBuffer(): string
    {
        return $this->buffer;
    }

    /**
     * @param  string $buffer
     * @return void
     */
    private function setBuffer(string $buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * @return int
     */
    private function getIp(): int
    {
        return $this->ip;
    }

    /**
     * @param int $ip
     * @return void
     */
    private function setIp(int $ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return void
     */
    private function incrementIp()
    {
        $this->ip++;
    }

    /**
     * @return void
     */
    private function decrementIp()
    {
        $this->ip--;
    }

    /**
     * @return int
     */
    private function current()
    {
        return ord($this->buffer[$this->getIp()]);
    }

    /**
     * @return void
     */
    private function processOpcode()
    {
        switch ($this->current()) {
            case Opcode::MOVB_IMM8_TO_R0:
            case Opcode::MOVB_IMM8_TO_R1:
            case Opcode::MOVB_IMM8_TO_R2:
            case Opcode::MOVB_IMM8_TO_R3:
                $this->processBinaryMovbImm8ToRegs();
                break;
            case Opcode::ADDB_IMM8_TO_R0:
            case Opcode::ADDB_IMM8_TO_R1:
            case Opcode::ADDB_IMM8_TO_R2:
            case Opcode::ADDB_IMM8_TO_R3:
                $this->processBinaryAddbImm8ToRegs();
                break;
            case Opcode::SUBB_IMM8_TO_R0:
            case Opcode::SUBB_IMM8_TO_R1:
            case Opcode::SUBB_IMM8_TO_R2:
            case Opcode::SUBB_IMM8_TO_R3:
                $this->processBinarySubbImm8ToRegs();
                break;
            case Opcode::MULB_IMM8_TO_R0:
            case Opcode::MULB_IMM8_TO_R1:
            case Opcode::MULB_IMM8_TO_R2:
            case Opcode::MULB_IMM8_TO_R3:
                $this->processBinaryMulbImm8ToRegs();
                break;
            case Opcode::DIVB_IMM8_TO_R0:
            case Opcode::DIVB_IMM8_TO_R1:
            case Opcode::DIVB_IMM8_TO_R2:
            case Opcode::DIVB_IMM8_TO_R3:
                $this->processBinaryDivbImm8ToRegs();
                break;
            case Opcode::CMPB_IMM8_TO_R0:
            case Opcode::CMPB_IMM8_TO_R1:
            case Opcode::CMPB_IMM8_TO_R2:
            case Opcode::CMPB_IMM8_TO_R3:
                $this->processBinaryCmpbImm8ToRegs();
                break;
            case Opcode::MOVB_R0_TO_R0:
            case Opcode::MOVB_R1_TO_R0:
            case Opcode::MOVB_R2_TO_R0:
            case Opcode::MOVB_R3_TO_R0:
                $this->processBinaryMovbRegsToR0();
                break;
            case Opcode::MOVB_R0_TO_R1:
            case Opcode::MOVB_R1_TO_R1:
            case Opcode::MOVB_R2_TO_R1:
            case Opcode::MOVB_R3_TO_R1:
                $this->processBinaryMovbRegsToR1();
                break;
            case Opcode::MOVB_R0_TO_R2:
            case Opcode::MOVB_R1_TO_R2:
            case Opcode::MOVB_R2_TO_R2:
            case Opcode::MOVB_R3_TO_R2:
                $this->processBinaryMovbRegsToR2();
                break;
            case Opcode::MOVB_R0_TO_R3:
            case Opcode::MOVB_R1_TO_R3:
            case Opcode::MOVB_R2_TO_R3:
            case Opcode::MOVB_R3_TO_R3:
                $this->processBinaryMovbRegsToR3();
                break;
            case Opcode::ADDB_R0_TO_R0:
            case Opcode::ADDB_R1_TO_R0:
            case Opcode::ADDB_R2_TO_R0:
            case Opcode::ADDB_R3_TO_R0:
                $this->processBinaryAddbRegsToR0();
                break;
            case Opcode::ADDB_R0_TO_R1:
            case Opcode::ADDB_R1_TO_R1:
            case Opcode::ADDB_R2_TO_R1:
            case Opcode::ADDB_R3_TO_R1:
                $this->processBinaryAddbRegsToR1();
                break;
            case Opcode::ADDB_R0_TO_R2:
            case Opcode::ADDB_R1_TO_R2:
            case Opcode::ADDB_R2_TO_R2:
            case Opcode::ADDB_R3_TO_R2:
                $this->processBinaryAddbRegsToR2();
                break;
            case Opcode::ADDB_R0_TO_R3:
            case Opcode::ADDB_R1_TO_R3:
            case Opcode::ADDB_R2_TO_R3:
            case Opcode::ADDB_R3_TO_R3:
                $this->processBinaryAddbRegsToR3();
                break;
            case Opcode::SUBB_R0_TO_R0:
            case Opcode::SUBB_R1_TO_R0:
            case Opcode::SUBB_R2_TO_R0:
            case Opcode::SUBB_R3_TO_R0:
                $this->processBinarySubbRegsToR0();
                break;
            case Opcode::SUBB_R0_TO_R1:
            case Opcode::SUBB_R1_TO_R1:
            case Opcode::SUBB_R2_TO_R1:
            case Opcode::SUBB_R3_TO_R1:
                $this->processBinarySubbRegsToR1();
                break;
            case Opcode::SUBB_R0_TO_R2:
            case Opcode::SUBB_R1_TO_R2:
            case Opcode::SUBB_R2_TO_R2:
            case Opcode::SUBB_R3_TO_R2:
                $this->processBinarySubbRegsToR2();
                break;
            case Opcode::SUBB_R0_TO_R3:
            case Opcode::SUBB_R1_TO_R3:
            case Opcode::SUBB_R2_TO_R3:
            case Opcode::SUBB_R3_TO_R3:
                $this->processBinarySubbRegsToR3();
                break;
            case Opcode::MULB_R0_TO_R0:
            case Opcode::MULB_R1_TO_R0:
            case Opcode::MULB_R2_TO_R0:
            case Opcode::MULB_R3_TO_R0:
                $this->processBinaryMulbRegsToR0();
                break;
            case Opcode::MULB_R0_TO_R1:
            case Opcode::MULB_R1_TO_R1:
            case Opcode::MULB_R2_TO_R1:
            case Opcode::MULB_R3_TO_R1:
                $this->processBinaryMulbRegsToR1();
                break;
            case Opcode::MULB_R0_TO_R2:
            case Opcode::MULB_R1_TO_R2:
            case Opcode::MULB_R2_TO_R2:
            case Opcode::MULB_R3_TO_R2:
                $this->processBinaryMulbRegsToR2();
                break;
            case Opcode::MULB_R0_TO_R3:
            case Opcode::MULB_R1_TO_R3:
            case Opcode::MULB_R2_TO_R3:
            case Opcode::MULB_R3_TO_R3:
                $this->processBinaryMulbRegsToR3();
                break;
            case Opcode::DIVB_R0_TO_R0:
            case Opcode::DIVB_R1_TO_R0:
            case Opcode::DIVB_R2_TO_R0:
            case Opcode::DIVB_R3_TO_R0:
                $this->processBinaryDivbRegsToR0();
                break;
            case Opcode::DIVB_R0_TO_R1:
            case Opcode::DIVB_R1_TO_R1:
            case Opcode::DIVB_R2_TO_R1:
            case Opcode::DIVB_R3_TO_R1:
                $this->processBinaryDivbRegsToR1();
                break;
            case Opcode::DIVB_R0_TO_R2:
            case Opcode::DIVB_R1_TO_R2:
            case Opcode::DIVB_R2_TO_R2:
            case Opcode::DIVB_R3_TO_R2:
                $this->processBinaryDivbRegsToR2();
                break;
            case Opcode::DIVB_R0_TO_R3:
            case Opcode::DIVB_R1_TO_R3:
            case Opcode::DIVB_R2_TO_R3:
            case Opcode::DIVB_R3_TO_R3:
                $this->processBinaryDivbRegsToR3();
                break;
            case Opcode::CMPB_R0_TO_R0:
            case Opcode::CMPB_R1_TO_R0:
            case Opcode::CMPB_R2_TO_R0:
            case Opcode::CMPB_R3_TO_R0:
                $this->processBinaryCmpbRegsToR0();
                break;
            case Opcode::CMPB_R0_TO_R1:
            case Opcode::CMPB_R1_TO_R1:
            case Opcode::CMPB_R2_TO_R1:
            case Opcode::CMPB_R3_TO_R1:
                $this->processBinaryCmpbRegsToR1();
                break;
            case Opcode::CMPB_R0_TO_R2:
            case Opcode::CMPB_R1_TO_R2:
            case Opcode::CMPB_R2_TO_R2:
            case Opcode::CMPB_R3_TO_R2:
                $this->processBinaryCmpbRegsToR2();
                break;
            case Opcode::CMPB_R0_TO_R3:
            case Opcode::CMPB_R1_TO_R3:
            case Opcode::CMPB_R2_TO_R3:
            case Opcode::CMPB_R3_TO_R3:
                $this->processBinaryCmpbRegsToR3();
                break;
            case Opcode::PRIB_R0:
            case Opcode::PRIB_R1:
            case Opcode::PRIB_R2:
            case Opcode::PRIB_R3:
                $this->processUnaryPribRegs();
                break;
            case Opcode::PRIB_IMM8:
                $this->processUnaryPribImm8();
                break;
            case Opcode::JUMP_REX_PREFIX:
                $this->processJumpRexPrefix();
                break;
            case Opcode::HALT:
                $this->halt();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMovbImm8ToRegs()
    {
        switch ($this->current()) {
            case Opcode::MOVB_IMM8_TO_R0:
                $this->processBinaryMovbImm8ToR0();
                break;
            case Opcode::MOVB_IMM8_TO_R1:
                $this->processBinaryMovbImm8ToR1();
                break;
            case Opcode::MOVB_IMM8_TO_R2:
                $this->processBinaryMovbImm8ToR2();
                break;
            case Opcode::MOVB_IMM8_TO_R3:
                $this->processBinaryMovbImm8ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryAddbImm8ToRegs()
    {
        switch ($this->current()) {
            case Opcode::ADDB_IMM8_TO_R0:
                $this->processBinaryAddbImm8ToR0();
                break;
            case Opcode::ADDB_IMM8_TO_R1:
                $this->processBinaryAddbImm8ToR1();
                break;
            case Opcode::ADDB_IMM8_TO_R2:
                $this->processBinaryAddbImm8ToR2();
                break;
            case Opcode::ADDB_IMM8_TO_R3:
                $this->processBinaryAddbImm8ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinarySubbImm8ToRegs()
    {
        switch ($this->current()) {
            case Opcode::SUBB_IMM8_TO_R0:
                $this->processBinarySubbImm8ToR0();
                break;
            case Opcode::SUBB_IMM8_TO_R1:
                $this->processBinarySubbImm8ToR1();
                break;
            case Opcode::SUBB_IMM8_TO_R2:
                $this->processBinarySubbImm8ToR2();
                break;
            case Opcode::SUBB_IMM8_TO_R3:
                $this->processBinarySubbImm8ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMulbImm8ToRegs()
    {
        switch ($this->current()) {
            case Opcode::MULB_IMM8_TO_R0:
                $this->processBinaryMulbImm8ToR0();
                break;
            case Opcode::MULB_IMM8_TO_R1:
                $this->processBinaryMulbImm8ToR1();
                break;
            case Opcode::MULB_IMM8_TO_R2:
                $this->processBinaryMulbImm8ToR2();
                break;
            case Opcode::MULB_IMM8_TO_R3:
                $this->processBinaryMulbImm8ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryDivbImm8ToRegs()
    {
        switch ($this->current()) {
            case Opcode::DIVB_IMM8_TO_R0:
                $this->processBinaryDivbImm8ToR0();
                break;
            case Opcode::DIVB_IMM8_TO_R1:
                $this->processBinaryDivbImm8ToR1();
                break;
            case Opcode::DIVB_IMM8_TO_R2:
                $this->processBinaryDivbImm8ToR2();
                break;
            case Opcode::DIVB_IMM8_TO_R3:
                $this->processBinaryDivbImm8ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryCmpbImm8ToRegs()
    {
        switch ($this->current()) {
            case Opcode::CMPB_IMM8_TO_R0:
                $this->processBinaryCmpbImm8ToR0();
                break;
            case Opcode::CMPB_IMM8_TO_R1:
                $this->processBinaryCmpbImm8ToR1();
                break;
            case Opcode::CMPB_IMM8_TO_R2:
                $this->processBinaryCmpbImm8ToR2();
                break;
            case Opcode::CMPB_IMM8_TO_R3:
                $this->processBinaryCmpbImm8ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMovbRegsToR0()
    {
        switch ($this->current()) {
            case Opcode::MOVB_R0_TO_R0:
                $this->processBinaryMovbR0ToR0();
                break;
            case Opcode::MOVB_R1_TO_R0:
                $this->processBinaryMovbR1ToR0();
                break;
            case Opcode::MOVB_R2_TO_R0:
                $this->processBinaryMovbR2ToR0();
                break;
            case Opcode::MOVB_R3_TO_R0:
                $this->processBinaryMovbR3ToR0();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMovbRegsToR1()
    {
        switch ($this->current()) {
            case Opcode::MOVB_R0_TO_R1:
                $this->processBinaryMovbR0ToR1();
                break;
            case Opcode::MOVB_R1_TO_R1:
                $this->processBinaryMovbR1ToR1();
                break;
            case Opcode::MOVB_R2_TO_R1:
                $this->processBinaryMovbR2ToR1();
                break;
            case Opcode::MOVB_R3_TO_R1:
                $this->processBinaryMovbR3ToR1();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMovbRegsToR2()
    {
        switch ($this->current()) {
            case Opcode::MOVB_R0_TO_R2:
                $this->processBinaryMovbR0ToR2();
                break;
            case Opcode::MOVB_R1_TO_R2:
                $this->processBinaryMovbR1ToR2();
                break;
            case Opcode::MOVB_R2_TO_R2:
                $this->processBinaryMovbR2ToR2();
                break;
            case Opcode::MOVB_R3_TO_R2:
                $this->processBinaryMovbR3ToR2();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMovbRegsToR3()
    {
        switch ($this->current()) {
            case Opcode::MOVB_R0_TO_R3:
                $this->processBinaryMovbR0ToR3();
                break;
            case Opcode::MOVB_R1_TO_R3:
                $this->processBinaryMovbR1ToR3();
                break;
            case Opcode::MOVB_R2_TO_R3:
                $this->processBinaryMovbR2ToR3();
                break;
            case Opcode::MOVB_R3_TO_R3:
                $this->processBinaryMovbR3ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryAddbRegsToR0()
    {
        switch ($this->current()) {
            case Opcode::ADDB_R0_TO_R0:
                $this->processBinaryAddbR0ToR0();
                break;
            case Opcode::ADDB_R1_TO_R0:
                $this->processBinaryAddbR1ToR0();
                break;
            case Opcode::ADDB_R2_TO_R0:
                $this->processBinaryAddbR2ToR0();
                break;
            case Opcode::ADDB_R3_TO_R0:
                $this->processBinaryAddbR3ToR0();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryAddbRegsToR1()
    {
        switch ($this->current()) {
            case Opcode::ADDB_R0_TO_R1:
                $this->processBinaryAddbR0ToR1();
                break;
            case Opcode::ADDB_R1_TO_R1:
                $this->processBinaryAddbR1ToR1();
                break;
            case Opcode::ADDB_R2_TO_R1:
                $this->processBinaryAddbR2ToR1();
                break;
            case Opcode::ADDB_R3_TO_R1:
                $this->processBinaryAddbR3ToR1();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryAddbRegsToR2()
    {
        switch ($this->current()) {
            case Opcode::ADDB_R0_TO_R2:
                $this->processBinaryAddbR0ToR2();
                break;
            case Opcode::ADDB_R1_TO_R2:
                $this->processBinaryAddbR1ToR2();
                break;
            case Opcode::ADDB_R2_TO_R2:
                $this->processBinaryAddbR2ToR2();
                break;
            case Opcode::ADDB_R3_TO_R2:
                $this->processBinaryAddbR3ToR2();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryAddbRegsToR3()
    {
        switch ($this->current()) {
            case Opcode::ADDB_R0_TO_R3:
                $this->processBinaryAddbR0ToR3();
                break;
            case Opcode::ADDB_R1_TO_R3:
                $this->processBinaryAddbR1ToR3();
                break;
            case Opcode::ADDB_R2_TO_R3:
                $this->processBinaryAddbR2ToR3();
                break;
            case Opcode::ADDB_R3_TO_R3:
                $this->processBinaryAddbR3ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinarySubbRegsToR0()
    {
        switch ($this->current()) {
            case Opcode::SUBB_R0_TO_R0:
                $this->processBinarySubbR0ToR0();
                break;
            case Opcode::SUBB_R1_TO_R0:
                $this->processBinarySubbR1ToR0();
                break;
            case Opcode::SUBB_R2_TO_R0:
                $this->processBinarySubbR2ToR0();
                break;
            case Opcode::SUBB_R3_TO_R0:
                $this->processBinarySubbR3ToR0();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinarySubbRegsToR1()
    {
        switch ($this->current()) {
            case Opcode::SUBB_R0_TO_R1:
                $this->processBinarySubbR0ToR1();
                break;
            case Opcode::SUBB_R1_TO_R1:
                $this->processBinarySubbR1ToR1();
                break;
            case Opcode::SUBB_R2_TO_R1:
                $this->processBinarySubbR2ToR1();
                break;
            case Opcode::SUBB_R3_TO_R1:
                $this->processBinarySubbR3ToR1();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinarySubbRegsToR2()
    {
        switch ($this->current()) {
            case Opcode::SUBB_R0_TO_R2:
                $this->processBinarySubbR0ToR2();
                break;
            case Opcode::SUBB_R1_TO_R2:
                $this->processBinarySubbR1ToR2();
                break;
            case Opcode::SUBB_R2_TO_R2:
                $this->processBinarySubbR2ToR2();
                break;
            case Opcode::SUBB_R3_TO_R2:
                $this->processBinarySubbR3ToR2();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinarySubbRegsToR3()
    {
        switch ($this->current()) {
            case Opcode::SUBB_R0_TO_R3:
                $this->processBinarySubbR0ToR3();
                break;
            case Opcode::SUBB_R1_TO_R3:
                $this->processBinarySubbR1ToR3();
                break;
            case Opcode::SUBB_R2_TO_R3:
                $this->processBinarySubbR2ToR3();
                break;
            case Opcode::SUBB_R3_TO_R3:
                $this->processBinarySubbR3ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMulbRegsToR0()
    {
        switch ($this->current()) {
            case Opcode::MULB_R0_TO_R0:
                $this->processBinaryMulbR0ToR0();
                break;
            case Opcode::MULB_R1_TO_R0:
                $this->processBinaryMulbR1ToR0();
                break;
            case Opcode::MULB_R2_TO_R0:
                $this->processBinaryMulbR2ToR0();
                break;
            case Opcode::MULB_R3_TO_R0:
                $this->processBinaryMulbR3ToR0();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMulbRegsToR1()
    {
        switch ($this->current()) {
            case Opcode::MULB_R0_TO_R1:
                $this->processBinaryMulbR0ToR1();
                break;
            case Opcode::MULB_R1_TO_R1:
                $this->processBinaryMulbR1ToR1();
                break;
            case Opcode::MULB_R2_TO_R1:
                $this->processBinaryMulbR2ToR1();
                break;
            case Opcode::MULB_R3_TO_R1:
                $this->processBinaryMulbR3ToR1();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMulbRegsToR2()
    {
        switch ($this->current()) {
            case Opcode::MULB_R0_TO_R2:
                $this->processBinaryMulbR0ToR2();
                break;
            case Opcode::MULB_R1_TO_R2:
                $this->processBinaryMulbR1ToR2();
                break;
            case Opcode::MULB_R2_TO_R2:
                $this->processBinaryMulbR2ToR2();
                break;
            case Opcode::MULB_R3_TO_R2:
                $this->processBinaryMulbR3ToR2();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMulbRegsToR3()
    {
        switch ($this->current()) {
            case Opcode::MULB_R0_TO_R3:
                $this->processBinaryMulbR0ToR3();
                break;
            case Opcode::MULB_R1_TO_R3:
                $this->processBinaryMulbR1ToR3();
                break;
            case Opcode::MULB_R2_TO_R3:
                $this->processBinaryMulbR2ToR3();
                break;
            case Opcode::MULB_R3_TO_R3:
                $this->processBinaryMulbR3ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryDivbRegsToR0()
    {
        switch ($this->current()) {
            case Opcode::DIVB_R0_TO_R0:
                $this->processBinaryDivbR0ToR0();
                break;
            case Opcode::DIVB_R1_TO_R0:
                $this->processBinaryDivbR1ToR0();
                break;
            case Opcode::DIVB_R2_TO_R0:
                $this->processBinaryDivbR2ToR0();
                break;
            case Opcode::DIVB_R3_TO_R0:
                $this->processBinaryDivbR3ToR0();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryDivbRegsToR1()
    {
        switch ($this->current()) {
            case Opcode::DIVB_R0_TO_R1:
                $this->processBinaryDivbR0ToR1();
                break;
            case Opcode::DIVB_R1_TO_R1:
                $this->processBinaryDivbR1ToR1();
                break;
            case Opcode::DIVB_R2_TO_R1:
                $this->processBinaryDivbR2ToR1();
                break;
            case Opcode::DIVB_R3_TO_R1:
                $this->processBinaryDivbR3ToR1();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryDivbRegsToR2()
    {
        switch ($this->current()) {
            case Opcode::DIVB_R0_TO_R2:
                $this->processBinaryDivbR0ToR2();
                break;
            case Opcode::DIVB_R1_TO_R2:
                $this->processBinaryDivbR1ToR2();
                break;
            case Opcode::DIVB_R2_TO_R2:
                $this->processBinaryDivbR2ToR2();
                break;
            case Opcode::DIVB_R3_TO_R2:
                $this->processBinaryDivbR3ToR2();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryDivbRegsToR3()
    {
        switch ($this->current()) {
            case Opcode::DIVB_R0_TO_R3:
                $this->processBinaryDivbR0ToR3();
                break;
            case Opcode::DIVB_R1_TO_R3:
                $this->processBinaryDivbR1ToR3();
                break;
            case Opcode::DIVB_R2_TO_R3:
                $this->processBinaryDivbR2ToR3();
                break;
            case Opcode::DIVB_R3_TO_R3:
                $this->processBinaryDivbR3ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryCmpbRegsToR0()
    {
        switch ($this->current()) {
            case Opcode::CMPB_R0_TO_R0:
                $this->processBinaryCmpbR0ToR0();
                break;
            case Opcode::CMPB_R1_TO_R0:
                $this->processBinaryCmpbR1ToR0();
                break;
            case Opcode::CMPB_R2_TO_R0:
                $this->processBinaryCmpbR2ToR0();
                break;
            case Opcode::CMPB_R3_TO_R0:
                $this->processBinaryCmpbR3ToR0();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryCmpbRegsToR1()
    {
        switch ($this->current()) {
            case Opcode::CMPB_R0_TO_R1:
                $this->processBinaryCmpbR0ToR1();
                break;
            case Opcode::CMPB_R1_TO_R1:
                $this->processBinaryCmpbR1ToR1();
                break;
            case Opcode::CMPB_R2_TO_R1:
                $this->processBinaryCmpbR2ToR1();
                break;
            case Opcode::CMPB_R3_TO_R1:
                $this->processBinaryCmpbR3ToR1();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryCmpbRegsToR2()
    {
        switch ($this->current()) {
            case Opcode::CMPB_R0_TO_R2:
                $this->processBinaryCmpbR0ToR2();
                break;
            case Opcode::CMPB_R1_TO_R2:
                $this->processBinaryCmpbR1ToR2();
                break;
            case Opcode::CMPB_R2_TO_R2:
                $this->processBinaryCmpbR2ToR2();
                break;
            case Opcode::CMPB_R3_TO_R2:
                $this->processBinaryCmpbR3ToR2();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryCmpbRegsToR3()
    {
        switch ($this->current()) {
            case Opcode::CMPB_R0_TO_R3:
                $this->processBinaryCmpbR0ToR3();
                break;
            case Opcode::CMPB_R1_TO_R3:
                $this->processBinaryCmpbR1ToR3();
                break;
            case Opcode::CMPB_R2_TO_R3:
                $this->processBinaryCmpbR2ToR3();
                break;
            case Opcode::CMPB_R3_TO_R3:
                $this->processBinaryCmpbR3ToR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processUnaryPribRegs()
    {
        switch ($this->current()) {
            case Opcode::PRIB_R0:
                $this->processUnaryPribR0();
                break;
            case Opcode::PRIB_R1:
                $this->processUnaryPribR1();
                break;
            case Opcode::PRIB_R2:
                $this->processUnaryPribR2();
                break;
            case Opcode::PRIB_R3:
                $this->processUnaryPribR3();
                break;
        }
    }

    /**
     * @return void
     */
    private function processUnaryPribImm8()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();

        echo sprintf("%d\n", $sign == 0xfe ? $number : (-1 * $number));
    }

    /**
     * @return void
     */
    private function processJumpRexPrefix()
    {
        $this->incrementIp();
        $this->checkForEOF();

        switch ($this->current()) {
            case JumpOpcode::JUMP_PLAIN:
                $this->processPlainJumpInstruction();
                break;
            case JumpOpcode::JUMP_IF_NOT_EQUAL:
                $this->processJumpIfNotEqualInstruction();
                break;
            case JumpOpcode::JUMP_IF_EQUAL:
                $this->processJumpIfEqualInstruction();
                break;
            case JumpOpcode::JUMP_IF_NOT_ZERO:
                $this->processJumpIfNotZeroInstruction();
                break;
            case JumpOpcode::JUMP_IF_ZERO:
                $this->processJumpIfZeroInstruction();
                break;
            case JumpOpcode::JUMP_IF_GREATER:
                $this->processJumpIfGreaterInstruction();
                break;
            case JumpOpcode::JUMP_IF_GREATER_OR_EQUAL:
                $this->processJumpIfGreaterOrEqualInstruction();
                break;
            case JumpOpcode::JUMP_IF_LESS:
                $this->processJumpIfLessInstruction();
                break;
            case JumpOpcode::JUMP_IF_LESS_OR_EQUAL:
                $this->processJumpIfLessOrEqualInstruction();
                break;
        }
    }

    /**
     * @return void
     */
    private function processBinaryMovbImm8ToR0()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $this->getRegister()->setR0($sign == 0xfe ? $number : (-1 * $number));
    }

    /**
     * @return void
     */
    private function processBinaryMovbImm8ToR1()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $this->getRegister()->setR1($sign == 0xfe ? $number : (-1 * $number));
    }

    /**
     * @return void
     */
    private function processBinaryMovbImm8ToR2()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $this->getRegister()->setR2($sign == 0xfe ? $number : (-1 * $number));
    }

    /**
     * @return void
     */
    private function processBinaryMovbImm8ToR3()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $this->getRegister()->setR3($sign == 0xfe ? $number : (-1 * $number));
    }

    /**
     * @return void
     */
    private function processBinaryAddbImm8ToR0()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR0($this->getRegister()->getR0() + $number);
    }

    /**
     * @return void
     */
    private function processBinaryAddbImm8ToR1()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR1($this->getRegister()->getR1() + $number);
    }

    /**
     * @return void
     */
    private function processBinaryAddbImm8ToR2()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR2($this->getRegister()->getR2() + $number);
    }

    /**
     * @return void
     */
    private function processBinaryAddbImm8ToR3()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR3($this->getRegister()->getR3() + $number);
    }

    /**
     * @return void
     */
    private function processBinarySubbImm8ToR0()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR0($this->getRegister()->getR0() - $number);
    }

    /**
     * @return void
     */
    private function processBinarySubbImm8ToR1()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR1($this->getRegister()->getR1() - $number);
    }

    /**
     * @return void
     */
    private function processBinarySubbImm8ToR2()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR2($this->getRegister()->getR2() - $number);
    }

    /**
     * @return void
     */
    private function processBinarySubbImm8ToR3()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR3($this->getRegister()->getR3() - $number);
    }

    /**
     * @return int
     */
    private function processBinaryMulbImm8ToR0()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR0($this->getRegister()->getR0() * $number);
    }

    /**
     * @return void
     */
    private function processBinaryMulbImm8ToR1()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR1($this->getRegister()->getR1() * $number);
    }

    /**
     * @return void
     */
    private function processBinaryMulbImm8ToR2()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR2($this->getRegister()->getR2() * $number);
    }

    /**
     * @return void
     */
    private function processBinaryMulbImm8ToR3()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);

        $this->getRegister()
            ->setR3($this->getRegister()->getR3() * $number);
    }

    /**
     * @return void
     */
    private function processBinaryDivbImm8ToR0()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign === 0xfe ? $number : (-1 * $number);

        if (!$number) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR0() / $number);
        $this->getRegister()->setR0($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbImm8ToR1()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign === 0xfe ? $number : (-1 * $number);

        if (!$number) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR1() / $number);
        $this->getRegister()->setR1($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbImm8ToR2()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign === 0xfe ? $number : (-1 * $number);

        if (!$number) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR2() / $number);
        $this->getRegister()->setR2($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbImm8ToR3()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign === 0xfe ? $number : (-1 * $number);

        if (!$number) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR3() / $number);
        $this->getRegister()->setR3($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbImm8ToR0()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign === 0xfe ? $number : (-1 * $number);
        $result = $this->getRegister()->getR0() - $number;

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbImm8ToR1()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign === 0xfe ? $number : (-1 * $number);
        $result = $this->getRegister()->getR1() - $number;

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbImm8ToR2()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign === 0xfe ? $number : (-1 * $number);
        $result = $this->getRegister()->getR2() - $number;

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbImm8ToR3()
    {
        try {
            $sign = $this->getAndCheckForNumberSign();
        } catch (RuntimeException $e) {
            throw $e;
        }

        $number = $this->deserializeVanillaNumber();
        $number = $sign == 0xfe ? $number : (-1 * $number);
        $result = $this->getRegister()->getR3() - $number;

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryMovbR0ToR0()
    {
        $this->getRegister()->setR0($this->getRegister()->getR0());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR1ToR0()
    {
        $this->getRegister()->setR0($this->getRegister()->getR1());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR2ToR0()
    {
        $this->getRegister()->setR0($this->getRegister()->getR2());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR3ToR0()
    {
        $this->getRegister()->setR0($this->getRegister()->getR3());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR0ToR1()
    {
        $this->getRegister()->setR1($this->getRegister()->getR0());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR1ToR1()
    {
        $this->getRegister()->setR1($this->getRegister()->getR1());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR2ToR1()
    {
        $this->getRegister()->setR1($this->getRegister()->getR2());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR3ToR1()
    {
        $this->getRegister()->setR1($this->getRegister()->getR3());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR0ToR2()
    {
        $this->getRegister()->setR2($this->getRegister()->getR0());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR1ToR2()
    {
        $this->getRegister()->setR2($this->getRegister()->getR1());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR2ToR2()
    {
        $this->getRegister()->setR2($this->getRegister()->getR2());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR3ToR2()
    {
        $this->getRegister()->setR2($this->getRegister()->getR3());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR0ToR3()
    {
        $this->getRegister()->setR3($this->getRegister()->getR0());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR1ToR3()
    {
        $this->getRegister()->setR3($this->getRegister()->getR1());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR2ToR3()
    {
        $this->getRegister()->setR3($this->getRegister()->getR2());
    }

    /**
     * @return void
     */
    private function processBinaryMovbR3ToR3()
    {
        $this->getRegister()->setR3($this->getRegister()->getR3());
    }

    /**
     * @return void
     */
    private function processBinaryAddbR0ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() + $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR1ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() + $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR2ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() + $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR3ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() + $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR0ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() + $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR1ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() + $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR2ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() + $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR3ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() + $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR0ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() + $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR1ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() + $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR2ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() + $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR3ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() + $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR0ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() + $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR1ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() + $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR2ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() + $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinaryAddbR3ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() + $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR0ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() - $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR1ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() - $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR2ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() - $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR3ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() - $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR0ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() - $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR1ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() - $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR2ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() - $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR3ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() - $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR0ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() - $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR1ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() - $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR2ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() - $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR3ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() - $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR0ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() - $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR1ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() - $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR2ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() - $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinarySubbR3ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() - $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR0ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() * $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR1ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() * $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR2ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() * $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR3ToR0()
    {
        $this->getRegister()->setR0(
            $this->getRegister()->getR0() * $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR0ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() * $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR1ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() * $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR2ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() * $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR3ToR1()
    {
        $this->getRegister()->setR1(
            $this->getRegister()->getR1() * $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR0ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() * $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR1ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() * $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR2ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() * $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR3ToR2()
    {
        $this->getRegister()->setR2(
            $this->getRegister()->getR2() * $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR0ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() * $this->getRegister()->getR0()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR1ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() * $this->getRegister()->getR1()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR2ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() * $this->getRegister()->getR2()
        );
    }

    /**
     * @return void
     */
    private function processBinaryMulbR3ToR3()
    {
        $this->getRegister()->setR3(
            $this->getRegister()->getR3() * $this->getRegister()->getR3()
        );
    }

    /**
     * @return void
     */
    private function processBinaryDivbR0ToR0()
    {
        if (!$this->getRegister()->getR0()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR0() / $this->getRegister()->getR0());

        $this->getRegister()->setR0($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR1ToR0()
    {
        if (!$this->getRegister()->getR1()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR0() / $this->getRegister()->getR1());

        $this->getRegister()->setR0($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR2ToR0()
    {
        if (!$this->getRegister()->getR2()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR0() / $this->getRegister()->getR2());

        $this->getRegister()->setR0($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR3ToR0()
    {
        if (!$this->getRegister()->getR3()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR0() / $this->getRegister()->getR3());

        $this->getRegister()->setR0($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR0ToR1()
    {
        if (!$this->getRegister()->getR0()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR1() / $this->getRegister()->getR0());

        $this->getRegister()->setR1($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR1ToR1()
    {
        if (!$this->getRegister()->getR1()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR1() / $this->getRegister()->getR1());

        $this->getRegister()->setR1($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR2ToR1()
    {
        if (!$this->getRegister()->getR2()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR1() / $this->getRegister()->getR2());

        $this->getRegister()->setR1($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR3ToR1()
    {
        if (!$this->getRegister()->getR3()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR1() / $this->getRegister()->getR3());

        $this->getRegister()->setR1($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR0ToR2()
    {
        if (!$this->getRegister()->getR0()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR2() / $this->getRegister()->getR0());

        $this->getRegister()->setR2($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR1ToR2()
    {
        if (!$this->getRegister()->getR1()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR2() / $this->getRegister()->getR1());

        $this->getRegister()->setR2($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR2ToR2()
    {
        if (!$this->getRegister()->getR2()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR2() / $this->getRegister()->getR2());

        $this->getRegister()->setR2($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR3ToR2()
    {
        if (!$this->getRegister()->getR3()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR2() / $this->getRegister()->getR3());

        $this->getRegister()->setR2($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR0ToR3()
    {
        if (!$this->getRegister()->getR0()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR3() / $this->getRegister()->getR0());

        $this->getRegister()->setR3($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR1ToR3()
    {
        if (!$this->getRegister()->getR1()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR3() / $this->getRegister()->getR1());

        $this->getRegister()->setR3($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR2ToR3()
    {
        if (!$this->getRegister()->getR2()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR3() / $this->getRegister()->getR2());

        $this->getRegister()->setR3($result);
    }

    /**
     * @return void
     */
    private function processBinaryDivbR3ToR3()
    {
        if (!$this->getRegister()->getR3()) {
            throw new RuntimeException("Division by zero.");
        }

        $result = intval($this->getRegister()->getR3() / $this->getRegister()->getR3());

        $this->getRegister()->setR3($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR0ToR0()
    {
        $result = $this->getRegister()->getR0() - $this->getRegister()->getR0();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR1ToR0()
    {
        $result = $this->getRegister()->getR0() - $this->getRegister()->getR1();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR2ToR0()
    {
        $result = $this->getRegister()->getR0() - $this->getRegister()->getR2();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR3toR0()
    {
        $result = $this->getRegister()->getR0() - $this->getRegister()->getR3();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR0ToR1()
    {
        $result = $this->getRegister()->getR1() - $this->getRegister()->getR0();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR1ToR1()
    {
        $result = $this->getRegister()->getR1() - $this->getRegister()->getR1();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR2ToR1()
    {
        $result = $this->getRegister()->getR1() - $this->getRegister()->getR2();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR3ToR1()
    {
        $result = $this->getRegister()->getR1() - $this->getRegister()->getR3();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR0ToR2()
    {
        $result = $this->getRegister()->getR2() - $this->getRegister()->getR0();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR1ToR2()
    {
        $result = $this->getRegister()->getR2() - $this->getRegister()->getR1();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR2ToR2()
    {
        $result = $this->getRegister()->getR2() - $this->getRegister()->getR2();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR3ToR2()
    {
        $result = $this->getRegister()->getR2() - $this->getRegister()->getR3();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR0ToR3()
    {
        $result = $this->getRegister()->getR3() - $this->getRegister()->getR0();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR1ToR3()
    {
        $result = $this->getRegister()->getR3() - $this->getRegister()->getR1();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR2ToR3()
    {
        $result = $this->getRegister()->getR3() - $this->getRegister()->getR2();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processBinaryCmpbR3ToR3()
    {
        $result = $this->getRegister()->getR3() - $this->getRegister()->getR3();

        $this->clearEflagStatus();
        $this->setEflagStatusAfterComparation($result);
    }

    /**
     * @return void
     */
    private function processUnaryPribR0()
    {
        echo sprintf("%d\n", $this->getRegister()->getR0());
    }

    /**
     * @return void
     */
    private function processUnaryPribR1()
    {
        echo sprintf("%d\n", $this->getRegister()->getR1());
    }

    /**
     * @return void
     */
    private function processUnaryPribR2()
    {
        echo sprintf("%d\n", $this->getRegister()->getR2());
    }

    /**
     * @return void
     */
    private function processUnaryPribR3()
    {
        echo sprintf("%d\n", $this->getRegister()->getR3());
    }

    /**
     * @return void
     */
    private function processPlainJumpInstruction()
    {
        $this->setIp($this->deserializeVanillaNumber() - 1);
    }

    /**
     * @return void
     */
    private function processJumpIfNotEqualInstruction()
    {
        $this->processJumpIfNotZeroInstruction();
    }

    /**
     * @return void
     */
    private function processJumpIfEqualInstruction()
    {
        $this->processJumpIfZeroInstruction();
    }

    /**
     * @return void
     */
    private function processJumpIfNotZeroInstruction()
    {
        if ($this->getEflags()->isZero()) {
            return;
        }

        $this->setIp($this->deserializeVanillaNumber() - 1);
    }

    /**
     * @return void
     */
    private function processJumpIfZeroInstruction()
    {
        if (!$this->getEflags()->isZero()) {
            return;
        }

        $this->setIp($this->deserializeVanillaNumber() - 1);
    }

    /**
     * @return void
     */
    private function processJumpIfGreaterInstruction()
    {
        if (!$this->getEflags()->isGreat()) {
            return;
        }

        $this->setIp($this->deserializeVanillaNumber() - 1);
    }

    /**
     * @return void
     */
    private function processJumpIfGreaterOrEqualInstruction()
    {
        if (!$this->getEflags()->isGreat() &&
            !$this->getEflags()->isZero()) {
            return;
        }

        $this->setIp($this->deserializeVanillaNumber() - 1);
    }

    /**
     * @return void
     */
    private function processJumpIfLessInstruction()
    {
        if (!$this->getEflags()->isLess()) {
            return;
        }

        $this->setIp($this->deserializeVanillaNumber() - 1);
    }

    /**
     * @return void
     */
    private function processJumpIfLessOrEqualInstruction()
    {
        if (!$this->getEflags()->isLess() &&
            !$this->getEflags()->isZero()) {
            return;
        }

        $this->setIp($this->deserializeVanillaNumber() - 1);
    }

    /**
     * @return void
     */
    private function checkForEOF()
    {
        if ($this->isEOF()) {
            throw new RuntimeException(
                sprintf(
                    "Invalid next bytecode at offset %d.",
                    $this->ip - 1
                )
            );
        }
    }

    /**
     * @param  int $sign
     * @return int
     * @throws \Vm\RuntimeException If given number is not sign number.
     */
    private function getAndCheckForNumberSign()
    {
        $this->incrementIp();
        $this->checkForEOF();

        $sign = $this->current();

        if ($sign !== 0xfe && $sign !== 0xff) {
            throw new RuntimeException(
                sprintf(
                    "Invalid number sign bytecode at offset %d.",
                    $this->getIp()
                )
            );
        }

        return $sign;
    }

    /**
     * @return void
     */
    private function clearEflagStatus()
    {
        $flag = $this->getEflags()->getFlag();
        $flag &= ~EflagsInterface::ZERO;
        $flag &= ~EflagsInterface::GREAT;
        $flag &= ~EflagsInterface::LESS;

        $this->getEflags()->setFlag($flag);
    }

    /**
     * @param int $result
     * @return void
     */
    private function setEflagStatusAfterComparation(int $result)
    {
        if ($result < 0) {
            $this->getEflags()->setFlag(
                $this->getEflags()->getFlag() | EflagsInterface::LESS
            );

            return;
        }

        if ($result === 0) {
            $this->getEflags()->setFlag(
                $this->getEflags()->getFlag() | EflagsInterface::ZERO
            );

            return;
        }

        if ($result > 0) {
            $this->getEflags()->setFlag(
                $this->getEflags()->getFlag() | EflagsInterface::GREAT
            );

            return;
        }
    }

    /**
     * @return int
     */
    private function deserializeVanillaNumber()
    {
        $tmp = [];

        $this->incrementIp();
        $this->checkForEOF();

        $tmp[] = $this->current();

        $this->incrementIp();
        $this->checkForEOF();

        $tmp[] = $this->current();

        $this->incrementIp();
        $this->checkForEOF();

        $tmp[] = $this->current();

        $this->incrementIp();
        $this->checkForEOF();

        $tmp[] = $this->current();

        $result = 0       | (($tmp[0] & 0xff) << 24);
        $result = $result | (($tmp[1] & 0xff) << 16);
        $result = $result | (($tmp[2] & 0xff) <<  8);
        $result = $result | (($tmp[3] & 0xff) <<  0);

        return $result;
    }
}
