<?php

declare(strict_types=1);

namespace Vm;

use Vm\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface AstInterface
{
    /**
     * @var int
     */
    const AST_ROOT = 0;

    /**
     * @var int
     */
    const AST_MNEMONIC = 1;

    /**
     * @var int
     */
    const AST_REGISTER = 2;

    /**
     * @var int
     */
    const AST_INTEGER_VALUE = 3;

    /**
     * @var int
     */
    const AST_INSTRUCTION_LINE = 4;

    /**
     * @return \Vm\Node\NodeInterface|null
     */
    public function getValue(): ?NodeInterface;

    /**
     * @param  \Vm\Node\NodeInterface|null $value
     * @return void
     */
    public function setValue(?NodeInterface $value);

    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @param  int $type
     * @return void
     */
    public function setType(int $type);

    /**
     * @return array
     */
    public function getChilds(): array;

    /**
     * @param  array $childs
     * @return void
     */
    public function setChilds(array $childs);

    /**
     * @param  \Vm\AstInterface $value
     * @return void
     */
    public function addChild(AstInterface $value);
}
