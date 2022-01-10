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
    private $flagList = [];

    /**
     * @var int
     */
    private $flags;

    /**
     * {@inheritdoc}
     */
    public function calculate()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFlag(): int
    {
        return $this->flags;
    }

    /**
     * {@inheritdoc}
     */
    public function setFlag(int $flag)
    {
        $this->flags = $flag;
    }
}
