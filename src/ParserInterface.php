<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface ParserInterface
{
    /**
     * @param  string $buffer
     * @return void
     */
    public function parse(string $buffer);

    /**
     * @return \Vm\LexerInterface
     */
    public function getLexer(): LexerInterface;

    /**
     * @param  \Vm\LexerInterface $lexer
     * @return void
     */
    public function setLexer(LexerInterface $lexer);

    /**
     * @return array
     */
    public function getInput(): array;

    /**
     * @param  array $input
     * @return void
     */
    public function setInput(array $input);

    /**
     * @return \Vm\AstInterface
     */
    public function getAst(): AstInterface;

    /**
     * @param  \Vm\AstInterface $ast
     * @return void
     */
    public function setAst(AstInterface $ast);
}
