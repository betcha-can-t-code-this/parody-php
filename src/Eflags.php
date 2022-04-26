<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Eflags implements EflagsInterface
{
    /**
     * @var array
     */
    private $serialized = [];

    /**
     * @var int
     */
    private $flag = 0;

    /**
     * {@inheritdoc}
     */
    public function getFlag(): int
    {
        return $this->flag;
    }

    /**
     * {@inheritdoc}
     */
    public function setFlag(int $flag)
    {
        $this->flag = $flag;
    }

    /**
     * {@inheritdoc}
     */
    public function isCarry(): bool
    {
        return !($this->getFlag() & EflagsInterface::CARRY)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isParity(): bool
    {
        return !($this->getFlag() & EflagsInterface::PARITY)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAdjust(): bool
    {
        return !($this->getFlag() & EflagsInterface::ADJUST)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isZero(): bool
    {
        return ($this->getFlag() & EflagsInterface::ZERO) !== EflagsInterface::ZERO
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isSign(): bool
    {
        return !($this->getFlag() & EflagsInterface::SIGN)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isTrap(): bool
    {
        return !($this->getFlag() & EflagsInterface::TRAP)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isInterruptEnable(): bool
    {
        return !($this->getFlag() & EflagsInterface::INTERRUPT_ENABLE)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isDirection(): bool
    {
        return !($this->getFlag() & EflagsInterface::DIRECTION)
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isOverflow(): bool
    {
        return !($this->getFlag() & EflagsInterface::OVERFLOW)
            ? false
            : true;
    }
}
