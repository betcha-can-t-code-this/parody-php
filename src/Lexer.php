<?php

declare(strict_types=1);

namespace Vm;

use Vm\Exception\LexedEntityException;
use Vm\Exception\SyntaxException;
use Vm\Node\Comma;
use Vm\Node\Label;
use Vm\Node\Mnemonic;
use Vm\Node\Newline;
use Vm\Node\NodeInterface;
use Vm\Node\Number;
use Vm\Node\Register;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Lexer implements LexerInterface
{
    /**
     * @var string
     */
    private $input;

    /**
     * @var string
     */
    private $token = '';

    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var bool
     */
    private $isNegative;

    /**
     * @var \Vm\Node\NodeInterface[]
     */
    private $tokenObjects = [];

    /**
     * {@inheritdoc}
     */
    public function lex(string $buffer)
    {
        $this->input = $buffer;

        while (true) {
            if ($this->isEOF()) {
                $this->processWhenEOF();
                break;
            }

            if ($this->token === LexerInterface::T_SPACE ||
                $this->token === LexerInterface::T_TAB) {
                $this->token = '';
                continue;
            }

            if ($this->token === LexerInterface::T_NEWLINE) {
                $this->processNewline();
                $this->token = '';
                continue;
            }

            if ($this->token === LexerInterface::T_START_COMMENT_LINE) {
                $this->processCommentLine();
                $this->token = '';
                continue;
            }

            if ($this->token === LexerInterface::T_COMMA) {
                $this->processComma();
                $this->token = '';
                continue;
            }

            if ($this->token === LexerInterface::T_PREFIX_NUM) {
                $this->processInteger();
                $this->token = '';
                continue;
            }

            if ($this->token === LexerInterface::T_START_LABEL) {
                $this->processLabel();
                $this->token = '';
                continue;
            }

            if ($this->isValidInstruction($this->token) &&
                $this->current() !== LexerInterface::T_SPACE &&
                $this->current() !== LexerInterface::T_TAB &&
                $this->current() !== LexerInterface::T_NEWLINE) {
                while ($this->current() !== LexerInterface::T_SPACE &&
                       $this->current() !== LexerInterface::T_TAB &&
                       $this->current() !== LexerInterface::T_NEWLINE) {
                    $this->token .= $this->current();
                    $this->next();
                }

                if (!$this->isValidInstruction($this->token)) {
                    throw new SyntaxException(
                        sprintf(
                            "'%s' is not a valid mnemonic.",
                            $this->token
                        )
                    );
                }

                $this->processMnemonic();
                $this->token = '';
                continue;
            }

            if ($this->isValidInstruction($this->token)) {
                $this->processMnemonic();
                $this->token = '';
                continue;
            }

            if ($this->isValidRegister($this->token)) {
                $this->processRegister();
                $this->token = '';
                continue;
            }

            if (($this->current() === '' ||
                 $this->current() === ' ' ||
                 $this->current() === "\n") &&
                !empty($this->token) &&
                (!$this->isValidInstruction(rtrim($this->token)) &&
                 !$this->isValidRegister(rtrim($this->token)))) {
                throw new SyntaxException(
                    sprintf(
                        "Current 'lexeme' -> '%s' is not valid register or instruction.",
                        $this->token
                    )
                );
            }

            $this->token .= $this->current();
            $this->next();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenObjects(): array
    {
        return $this->tokenObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function addNode(NodeInterface $node)
    {
        $this->tokenObjects[] = $node;
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
     * @return string
     */
    private function current(): string
    {
        return $this->input[$this->position];
    }

    /**
     * @return string
     */
    private function peek(): string
    {
        return $this->input[$this->position + 1];
    }

    /**
     * @return bool
     */
    private function isEOF(): bool
    {
        return $this->position >= strlen($this->input);
    }

    /**
     * @return void
     */
    private function processComma()
    {
        $this->addNode(new Comma($this->token));
    }

    /**
     * @return void
     */
    private function processInteger()
    {
        if (!isset($this->input[$this->position])) {
            throw new SyntaxException(
                "Syntax error, '#' must be followed by number (either positive/negative)"
            );
        }

        $this->token = '';
        $this->isNegative = $this->current() === '-'
            ? true
            : false;

        if ($this->isNegative) {
            $this->next();
        }

        if ($this->isNegative && !isset($this->input[$this->position])) {
            throw new SyntaxException(
                "Syntax error, '-' must be followed by decimal digit."
            );
        }

        while (true) {
            if (!isset($this->input[$this->position])) {
                break;
            }

            if (!is_numeric($this->current()) || $this->current() === ',') {
                break;
            }

            $this->token .= $this->current();
            $this->next();
        }

        $this->addNode(
            new Number(
                $this->isNegative
                ? intval($this->token) * -1
                : intval($this->token)
            )
        );
    }

    /**
     * @return void
     */
    private function processLabel()
    {
        $gotException = false;

        while (true) {
            if ($this->isEOF() || $this->current() === LexerInterface::T_NEWLINE) {
                break;
            }

            if ($this->current() === LexerInterface::T_SPACE) {
                $gotException = true;
                break;
            }

            $this->token .= $this->current();
            $this->next();
        }

        if ($gotException) {
            throw new LexedEntityException(
                "Label name cannot contain whitespace characters."
            );

            return;
        }

        if ($this->token[strlen($this->token) - 1] !== ':' &&
            (!sizeof($this->tokenObjects) &&
             end($this->tokenObjects)->getType() !== NodeInterface::MNEMONIC)) {
            throw new LexedEntityException(
                "Label name must ended by colon."
            );
        }

        $this->token = substr(
            $this->token,
            1,
            $this->token[strlen($this->token) - 1] === ':'
                ? -1
                : strlen($this->token)
        );

        $this->addNode(new Label($this->token));
    }

    /**
     * @return void
     */
    private function processMnemonic()
    {
        $this->addNode(new Mnemonic($this->token));
    }

    /**
     * @return void
     */
    private function processNewline()
    {
        $this->addNode(new Newline($this->token));
    }

    /**
     * @return void
     */
    private function processCommentLine()
    {
        while (true) {
            if ($this->isEOF()) {
                break;
            }

            if ($this->current() === "\n") {
                $this->token = $this->current();
                break;
            }

            // ignore the current token except newline
            // because this is in a scope of comment
            // section.
            $this->next();
        }

        if ($this->token === "\n") {
            $this->processNewline();
        }
    }

    /**
     * @return void
     */
    private function processRegister()
    {
        $this->addNode(new Register($this->token));
    }

    /**
     * @return void
     */
    private function processWhenEOF()
    {
        if ($this->isValidInstruction($this->token)) {
            $this->processMnemonic();
        }

        if ($this->isValidRegister($this->token)) {
            $this->processRegister();
        }
    }

    /**
     * @return array
     */
    private function getValidInstructions(): array
    {
        return [
            "movb", "addb", "subb", "mulb",
            "divb", "prib", "cmpb",
            // increment and decrement registers (byte)
            "incb", "decb",
            // plain jump (near)
            "jmp",
            // conditional jump (depends on zero flag status).
            "je", "jne", "jz", "jnz",
            // conditional jump (depends on great flag / zero flag status).
            "jge", "jg",
            // conditional jump (depends on less flag / zero flag status).
            "jle", "jl",
            // 'or' bitwise
            "orb",
            // 'xor' bitwise
            "xorb",
            // halt the VM.
            "halt"
        ];
    }

    /**
     * @param  string $buffer
     * @return bool
     */
    private function isValidInstruction(string $buffer): bool
    {
        return in_array($buffer, $this->getValidInstructions(), true);
    }

    /**
     * @return array
     */
    private function getValidRegisters(): array
    {
        return ["r0", "r1", "r2", "r3"];
    }

    /**
     * @param  string $buffer
     * @return bool
     */
    private function isValidRegister(string $buffer): bool
    {
        return in_array($buffer, $this->getValidRegisters(), true);
    }

    /**
     * @param  string $buffer
     * @return void
     */
    private function ensureValidInstructionAndRegister(string $buffer)
    {
        if (!$this->isValidInstruction($buffer) &&
            !$this->isValidRegister($buffer)) {
            throw new LexedEntityException(
                "Current 'lexeme' is not valid register or instruction."
            );
        }
    }
}
