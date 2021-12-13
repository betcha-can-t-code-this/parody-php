<?php

declare(strict_types=1);

namespace Vm\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Newline extends AbstractNode
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return '<newline>';
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): int
    {
        return NodeInterface::NEWLINE;
    }
}
