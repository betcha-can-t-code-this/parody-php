<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Eflags implements EflagsInterface
{
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
    public function isZero(): bool
    {
        return ($this->getFlag() & EflagsInterface::ZERO) !== EflagsInterface::ZERO
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isGreat(): bool
    {
        return ($this->getFlag() & EflagsInterface::GREAT) !== EflagsInterface::GREAT
            ? false
            : true;
    }

    /**
     * {@inheritdoc}
     */
    public function isLess(): bool
    {
        return ($this->getFlag() & EflagsInterface::LESS) !== EflagsInterface::LESS
            ? false
            : true;
    }
}
