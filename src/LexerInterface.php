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
     * @var string
     */
    const T_SPACE = ' ';

    /**
     * @var string
     */
    const T_NEWLINE = "\n";

    /**
     * @var string
     */
    const T_TAB = "\t";

    /**
     * @var string
     */
    const T_START_COMMENT_LINE = ';';

    /**
     * @var string
     */
    const T_COMMA = ',';

    /**
     * @var string
     */
    const T_PREFIX_NUM = '#';

    /**
     * @var string
     */
    const T_START_LABEL = '@';

    /**
     * @var string
     */
    const T_COLON = ':';

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
