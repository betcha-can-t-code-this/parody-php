<?php

declare(strict_types=1);

namespace Vm\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Mnemonic extends AbstractNode
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return '<mnemonic>';
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return NodeInterface::MNEMONIC;
    }
}
