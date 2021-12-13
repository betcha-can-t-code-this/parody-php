<?php

declare(strict_types=1);

namespace Vm;

use Vm\Exception\LexedEntityException;
use Vm\Exception\SyntaxException;
use Vm\Node\Comma;
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

            if ($this->token === ' ') {
                $this->token = '';
                continue;
            }

            if ($this->token === "\n") {
                $this->processNewline();
                $this->token = '';
                continue;
            }

            if ($this->token === ',') {
                $this->processComma();
                $this->token = '';
                continue;
            }

            if ($this->token === '#') {
                $this->processInteger();
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
        "divb", "prib"
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
        if (!$this->isValidInstruction($buffer) 
            && !$this->isValidRegister($buffer)
        ) {
            throw new LexedEntityException("Current 'lexeme' is not valid register or instruction.");
        }
    }
}
