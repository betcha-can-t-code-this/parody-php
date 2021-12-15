<?php

declare(strict_types=1);

namespace Vm\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Label extends AbstractNode
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return '<label>';
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return NodeInterface::LABEL;
    }
}
