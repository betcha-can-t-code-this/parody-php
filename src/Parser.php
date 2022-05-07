<?php

declare(strict_types=1);

namespace Vm;

use Throwable;
use Vm\Exception\SyntaxException;
use Vm\Node\Comma;
use Vm\Node\Label;
use Vm\Node\Mnemonic;
use Vm\Node\Newline;
use Vm\Node\NodeInterface;

use function is_a;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
final class Parser implements ParserInterface
{
    /**
     * @var \Vm\Node\NodeInterface
     */
    private $token;

    /**
     * @var array
     */
    private $input;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var int
     */
    private $line = 0;

    /**
     * @var \Vm\LexerInterface
     */
    private $lexer;

    /**
     * @var \Vm\AstInterface
     */
    private $ast;

    /**
     * @param  \Vm\LexerInterface $lexer
     * @return static
     */
    public function __construct(LexerInterface $lexer)
    {
        $this->setLexer($lexer);
        $this->setAst(new Ast(AstInterface::AST_ROOT, null));
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $buffer)
    {
        try {
            $this->getLexer()->lex($buffer);
        } catch (Throwable $e) {
            throw $e;
        }

        $this->setInput($this->getLexer()->getTokenObjects());

        while (true) {
            if ($this->isEOF()) {
                break;
            }

            if (is_a($this->current(), Label::class)) {
                $this->processLabel();
            }

            if (is_a($this->current(), Mnemonic::class)) {
                $this->processInstructionLine();
            }

            $this->next();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLexer(): LexerInterface
    {
        return $this->lexer;
    }

    /**
     * {@inheritdoc}
     */
    public function setLexer(LexerInterface $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * {@inheritdoc}
     */
    public function getInput(): array
    {
        return $this->input;
    }

    /**
     * {@inheritdoc}
     */
    public function setInput(array $input)
    {
        $this->input = $input;
    }

    /**
     * {@inheritdoc}
     */
    public function getAst(): AstInterface
    {
        return $this->ast;
    }

    /**
     * {@inheritdoc}
     */
    public function setAst(AstInterface $ast)
    {
        $this->ast = $ast;
    }

    /**
     * @return \Vm\Node\NodeInterface
     */
    private function current(): NodeInterface
    {
        return $this->input[$this->position];
    }

    /**
     * @return void
     */
    private function next()
    {
        $this->position++;
    }

    /**
     * @return void
     */
    private function prev()
    {
        $this->position--;
    }

    /**
     * @return bool
     */
    private function isEOF(): bool
    {
        return $this->position >= sizeof($this->input);
    }

    /**
     * @return void
     */
    private function processLabel()
    {
        $this->getAst()->addChild(new Ast(AstInterface::AST_LABEL, $this->current()));
    }

    /**
     * @return void
     */
    private function processInstructionLine()
    {
        $tmp = [];

        while (true) {
            if ($this->isEOF()) {
                break;
            }

            if ($this->current()->getType() === NodeInterface::NEWLINE) {
                $this->line++;
                break;
            }

            $tmp[] = $this->current();
            $this->next();
        }

        if (!sizeof($tmp)) {
            return;
        }

        if (!is_a($tmp[0], Mnemonic::class)) {
            throw new SyntaxException(
                "Instruction line must be prefixed by valid mnemonic."
            );
        }

        $this->runInstructionLineValidator($tmp);

        $ast = new Ast(AstInterface::AST_INSTRUCTION_LINE, null);

        foreach ($tmp as $vnode) {
            if ($vnode->getType() === NodeInterface::COMMA) {
                continue;
            }

            $ast->addChild(new Ast($this->determineNodeType($vnode), $vnode));
        }

        $this->getAst()->addChild($ast);
    }

    /**
     * @param  array $insn
     * @return void
     */
    private function validateBinaryMovbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "movb" &&
            $insn[3]->getType() === NodeInterface::NUMBER) {
            throw new SyntaxException(
                sprintf(
                    "Number cannot be placed in second operand when it's mnemonic is 'movb' (line: %d).",
                    $this->line
                )
            );
        }

        if ($insn[0]->getValue() === "movb"
            && (($insn[1]->getType() !== NodeInterface::REGISTER
            || $insn[1]->getType() !== NodeInterface::NUMBER)
            && $insn[3]->getType() !== NodeInterface::REGISTER)
        ) {
            throw new SyntaxException(
                sprintf(
                    "First operand must be register or numeric constant, and second operand must be " .
                    "register (line: %d).",
                    $this->line
                )
            );
        }
    }

    /**
     * @param  array $insn
     * @return void
     */
    private function validateBinaryAddbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "addb" &&
            $insn[3]->getType() === NodeInterface::NUMBER) {
            throw new SyntaxException(
                sprintf(
                    "Number cannot be placed in second operand when it's mnemonic is 'addb' (line: %d).",
                    $this->line
                )
            );
        }

        if ($insn[0]->getValue() === "addb"
            && (($insn[1]->getType() !== NodeInterface::REGISTER
            || $insn[1]->getType() !== NodeInterface::NUMBER)
            && $insn[3]->getType() !== NodeInterface::REGISTER)
        ) {
            throw new SyntaxException(
                sprintf(
                    "First operand must be register or numeric constant, and second operand must be " .
                    "register (line: %d).",
                    $this->line
                )
            );
        }
    }

    /**
     * @param  array $insn
     * @return void
     */
    private function validateBinarySubbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "subb" &&
            $insn[3]->getType() === NodeInterface::NUMBER) {
            throw new SyntaxException(
                sprintf(
                    "Number cannot be placed in second operand when it's mnemonic is 'subb' (line: %d).",
                    $this->line
                )
            );
        }

        if ($insn[0]->getValue() === "subb"
            && (($insn[1]->getType() !== NodeInterface::REGISTER
            || $insn[1]->getType() !== NodeInterface::NUMBER)
            && $insn[3]->getType() !== NodeInterface::REGISTER)
        ) {
            throw new SyntaxException(
                sprintf(
                    "First operand must be register or numeric constant, and second operand must be " .
                    "register (line: %d).",
                    $this->line
                )
            );
        }
    }

    /**
     * @param  array $insn
     * @return void
     */
    private function validateBinaryMulbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "mulb" &&
            $insn[3]->getType() === NodeInterface::NUMBER) {
            throw new SyntaxException(
                sprintf(
                    "Number cannot be placed in second operand when it's mnemonic is 'mulb' (line: %d).",
                    $this->line
                )
            );
        }

        if ($insn[0]->getValue() === "mulb" &&
            (($insn[1]->getType() !== NodeInterface::REGISTER ||
              $insn[1]->getType() !== NodeInterface::NUMBER) &&
             $insn[3]->getType() !== NodeInterface::REGISTER)) {
            throw new SyntaxException(
                sprintf(
                    "First operand must be register or numeric constant, and second operand must be " .
                    "register (line: %d).",
                    $this->line
                )
            );
        }
    }

    /**
     * @param  array $insn
     * @return void
     */
    private function validateBinaryDivbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "divb" &&
            $insn[3]->getType() === NodeInterface::NUMBER) {
            throw new SyntaxException(
                sprintf(
                    "Number cannot be placed in second operand when it's mnemonic is 'divb' (line: %d).",
                    $this->line
                )
            );
        }

        if ($insn[0]->getValue() === "divb" &&
            (($insn[1]->getType() !== NodeInterface::REGISTER ||
              $insn[1]->getType() !== NodeInterface::NUMBER) &&
             $insn[3]->getType() !== NodeInterface::REGISTER)) {
            throw new SyntaxException(
                sprintf(
                    "First operand must be register or numeric constant, and second operand must be " .
                    "register (line: %d).",
                    $this->line
                )
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateBinaryBitwiseOrbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "orb" &&
            $insn[3]->getType() === NodeInterface::NUMBER) {
            throw new SyntaxException(
                sprintf(
                    "Number cannot be placed in second operand when it's mnemonic is 'orb' (line: %d).",
                    $this->line
                )
            );
        }

        if ($insn[0]->getValue() === "orb" &&
            (($insn[1]->getType() !== NodeInterface::REGISTER ||
              $insn[1]->getType() !== NodeInterface::NUMBER) &&
            $insn[3]->getType() !== NodeInterface::REGISTER)) {
            throw new SyntaxException(
                sprintf(
                    "First operand must be register or numeric constant, and second operand must be " .
                    "register (line: %d).",
                    $this->line
                )
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateBinaryBitwiseXorbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "xorb" &&
            $insn[3]->getType() === NodeInterface::NUMBER) {
            throw new SyntaxException(
                sprintf(
                    "Number cannot be placed in second operand when it's mnemonic is 'xorb' (line: %d).",
                    $this->line
                )
            );
        }

        if ($insn[0]->getValue() === "xorb" &&
            (($insn[1]->getType() !== NodeInterface::REGISTER ||
              $insn[1]->getType() !== NodeInterface::NUMBER) &&
            $insn[3]->getType() !== NodeInterface::REGISTER)) {
            throw new SyntaxException(
                sprintf(
                    "First operand must be register or numeric constant, and second operand must be " .
                    "register (line: %d).",
                    $this->line
                )
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateBinaryCmpbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "cmpb" &&
            $insn[3]->getType() === NodeInterface::NUMBER) {
            throw new SyntaxException(
                sprintf(
                    "Number cannot be placed in second operand when it's mnemonic is 'cmpb' (line: %d).",
                    $this->line
                )
            );
        }

        if ($insn[0]->getValue() == "cmpb" &&
            (($insn[1]->getType() !== NodeInterface::REGISTER ||
              $insn[1]->getType() !== NodeInterface::NUMBER) &&
             $insn[3]->getType() !== NodeInterface::REGISTER)) {
            throw new SyntaxException(
                sprintf(
                    "First operand must be register or numeric constant, and second operand must be " .
                    "register (line: %d).",
                    $this->line
                )
            );
        }
    }

    /**
     * @param  array $insn
     * @return void
     */
    private function validateUnaryPribInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "prib"
            && ($insn[1]->getType() !== NodeInterface::NUMBER && $insn[1]->getType() !== NodeInterface::REGISTER)
        ) {
            throw new SyntaxException(
                "'prib' instruction must be followed by register name or number."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryIncbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "incb" &&
            $insn[1]->getType() !== NodeInterface::REGISTER) {
            throw new SyntaxException(
                "'incb' instruction must be followed by register name or number."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryDecbInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "decb" &&
            $insn[1]->getType() !== NodeInterface::REGISTER) {
            throw new SyntaxException(
                "'decb' instruction must be followed by register name or number."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJmpInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "jmp" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'jmp' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJeInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "je" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'je' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJneInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "jne" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'jne' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJnzInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "jnz" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'jnz' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJzInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "jz" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'jz' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJgInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "jg" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'jg' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJgeInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "jge" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'jge' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJlInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "jl" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'jl' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     */
    private function validateUnaryJleInstruction(array $insn)
    {
        if ($insn[0]->getValue() === "jle" &&
            $insn[1]->getType() !== NodeInterface::LABEL) {
            throw new SyntaxException(
                "'jle' instruction must be followed by label name."
            );
        }
    }

    /**
     * @param array $insn
     * @return void
     * @intrinsic-stub
     */
    private function validateNullaryHaltInstruction(array $insn)
    {
    }

    /**
     * @param  array $insn
     * @return void
     */
    private function runInstructionLineValidator(array $insn)
    {
        switch (sizeof($insn)) {
            case 1:
                $this->validateNullaryHaltInstruction($insn);
                break;
            case 2:
                $this->validateUnaryPribInstruction($insn);
                $this->validateUnaryIncbInstruction($insn);
                $this->validateUnaryDecbInstruction($insn);
                $this->validateUnaryJmpInstruction($insn);
                $this->validateUnaryJeInstruction($insn);
                $this->validateUnaryJneInstruction($insn);
                $this->validateUnaryJnzInstruction($insn);
                $this->validateUnaryJzInstruction($insn);
                $this->validateUnaryJgInstruction($insn);
                $this->validateUnaryJgeInstruction($insn);
                $this->validateUnaryJlInstruction($insn);
                $this->validateUnaryJleInstruction($insn);
                break;
            case 4:
                $this->validateBinaryCmpbInstruction($insn);
                $this->validateBinaryMovbInstruction($insn);
                $this->validateBinaryAddbInstruction($insn);
                $this->validateBinarySubbInstruction($insn);
                $this->validateBinaryMulbInstruction($insn);
                $this->validateBinaryDivbInstruction($insn);
                $this->validateBinaryBitwiseOrbInstruction($insn);
                $this->validateBinaryBitwiseXorbInstruction($insn);
                break;
            default:
                throw new SyntaxException("Unknown instruction.");
        }
    }

    /**
     * @param  \Vm\Node\NodeInterface $node
     * @return int
     */
    private function determineNodeType(NodeInterface $node): int
    {
        switch ($node->getType()) {
            case NodeInterface::MNEMONIC:
                return AstInterface::AST_MNEMONIC;
            case NodeInterface::REGISTER:
                return AstInterface::AST_REGISTER;
            case NodeInterface::NUMBER:
                return AstInterface::AST_INTEGER_VALUE;
        }

        return 0;
    }
}
