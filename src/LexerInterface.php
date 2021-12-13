<?php

declare(strict_types=1);

namespace Vm;

use Vm\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface LexerInterface
{
    /**
     * @param  string $buffer
     * @return \Vm\Node\NodeInterface
     */
    public function lex(string $buffer);

    /**
     * @return \Vm\Node\NodeInterface[]
     */
    public function getTokenObjects(): array;

    /**
     * @param  \Vm\Node\NodeInterface $node
     * @return void
     */
    public function addNode(NodeInterface $node);
}
