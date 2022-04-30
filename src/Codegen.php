<?php

declare(strict_types=1);

namespace Vm;

use Vm\Exception\AstException;
use Vm\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Codegen implements CodegenInterface
{
    /**
     * @var \Vm\JumpLabelInterface;
     */
    private $jumpLabel;

    /**
     * @var array
     */
    private $patchJump = [];

    /**
     * @var array
     */
    private $patchLoop = [];

    /**
     * @param \Vm\JumpLabelInterface $jumpLabel
     * @return static
     */
    public function __construct(JumpLabelInterface $jumpLabel)
    {
        $this->jumpLabel = $jumpLabel;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(AstInterface $ast)
    {
        if ($ast->getType() !== AstInterface::AST_ROOT) {
            throw new AstException(
                "Current ast node is not root."
            );
        }

        $tmp = [];
        $gotException = false;

        foreach ($ast->getChilds() as $key => $vnode) {
            if ($vnode->getType() !== AstInterface::AST_INSTRUCTION_LINE &&
                $vnode->getType() !== AstInterface::AST_LABEL) {
                $gotException = true;
                break;
            }

            if ($vnode->getType() === AstInterface::AST_LABEL) {
                $this->jumpLabel->add(
                    $vnode->getValue()->getValue(),
                    sizeof($tmp) === 0 ? 0 : sizeof($tmp)
                );
            }

            $this->processInstructionLine($vnode, $tmp);
        }

        if ($gotException) {
            throw new AstException(
                "Current ast node is not instruction line or label."
            );
        }

        // patching jump instruction, if any.
        foreach ($this->patchJump as $el) {
            $offset = $this->jumpLabel->fetch($el[2]);

            if ($offset === -1) {
                continue;
            }

            $lists = $this->serializeVanillaNumberIntoDwordList($offset);
            $pOff  = $el[0];

            $tmp[$pOff + 0] = Opcode::JUMP_REX_PREFIX;
            $tmp[$pOff + 1] = $el[1];
            $tmp[$pOff + 2] = $lists[0];
            $tmp[$pOff + 3] = $lists[1];
            $tmp[$pOff + 4] = $lists[2];
            $tmp[$pOff + 5] = $lists[3];
        }

        return join(
            array_map(
                function ($el) {
                    return chr($el);
                },
                $tmp
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getJumpLabel(): JumpLabelInterface
    {
        return $this->jumpLabel;
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processInstructionLine(AstInterface $ast, array &$result)
    {
        if (sizeof($ast->getChilds()) === 1) {
            $this->processNullaryHaltInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "jmp") {
            $this->processUnaryJumpInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "je") {
            $this->processUnaryJumpIfEqualInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "jne") {
            $this->processUnaryJumpIfNotEqualInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "jnz") {
            $this->processUnaryJumpIfNotZeroInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "jz") {
            $this->processUnaryJumpIfZeroInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "jg") {
            $this->processUnaryJumpIfGreaterInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "jge") {
            $this->processUnaryJumpIfGreaterOrEqualInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "jl") {
            $this->processUnaryJumpIfLessInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "jle") {
            $this->processUnaryJumpIfLessOrEqualInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 2 &&
            $ast->getChilds()[0]->getValue()->getValue() === "prib") {
            $this->processUnaryPribInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 3 &&
            $ast->getChilds()[0]->getValue()->getValue() === "movb") {
            $this->processBinaryMovbInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 3 &&
            $ast->getChilds()[0]->getValue()->getValue() === "addb") {
            $this->processBinaryAddbInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 3 &&
            $ast->getChilds()[0]->getValue()->getValue() === "subb") {
            $this->processBinarySubbInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 3 &&
            $ast->getChilds()[0]->getValue()->getValue() === "mulb") {
            $this->processBinaryMulbInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 3 &&
            $ast->getChilds()[0]->getValue()->getValue() === "divb") {
            $this->processBinaryDivbInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) == 3 &&
            $ast->getChilds()[0]->getValue()->getValue() === "cmpb") {
            $this->processBinaryCmpbInstruction($ast, $result);
            return;
        }

        if (sizeof($ast->getChilds()) === 3 &&
            $ast->getChilds()[0]->getValue()->getValue() === "orb") {
            $this->processBinaryBitwiseOrbInstruction($ast, $result);
            return;
        }
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processNullaryHaltInstruction(AstInterface $ast, array &$result)
    {
        $result[] = Opcode::HALT;
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_PLAIN, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpIfEqualInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_IF_EQUAL, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpIfNotEqualInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_IF_NOT_EQUAL, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpIfNotZeroInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_IF_NOT_ZERO, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpIfZeroInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_IF_ZERO, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpIfGreaterInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_IF_GREATER, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpIfGreaterOrEqualInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_IF_GREATER_OR_EQUAL, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpIfLessInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_IF_LESS, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processUnaryJumpIfLessOrEqualInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() !== NodeInterface::LABEL) {
            throw new AstException(
                "Jump-related instruction must be followed by label name."
            );
        }

        $name              = $ast->getChilds()[1]->getValue()->getValue();
        $replacement       = [0x00, 0x00, 0x00, 0x00, 0x00, 0x00];
        $this->patchJump[] = [sizeof($result), JumpOpcode::JUMP_IF_LESS_OR_EQUAL, $name];
        $result            = array_merge($result, $replacement);
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processUnaryPribInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::NUMBER) {
            $number = $ast->getChilds()[1]
                ->getValue()
                ->getValue();
            $serialized = $this->serializeNumberIntoDwordList($number);
        }

        if (isset($serialized)) {
            $result = array_merge($result, [Opcode::PRIB_IMM8], $serialized);
            return;
        }

        switch ($ast->getChilds()[1]->getValue()->getValue()) {
            case 'r0':
                $result[] = Opcode::PRIB_R0;
                break;
            case 'r1':
                $result[] = Opcode::PRIB_R1;
                break;
            case 'r2':
                $result[] = Opcode::PRIB_R2;
                break;
            case 'r3':
                $result[] = Opcode::PRIB_R3;
                break;
        }
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processBinaryMovbInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::NUMBER) {
            $number = $ast->getChilds()[1]
                ->getValue()
                ->getValue();
            $serialized = $this->serializeNumberIntoDwordList($number);
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result = array_merge($result, [Opcode::MOVB_IMM8_TO_R0], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result = array_merge($result, [Opcode::MOVB_IMM8_TO_R1], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result = array_merge($result, [Opcode::MOVB_IMM8_TO_R2], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result = array_merge($result, [Opcode::MOVB_IMM8_TO_R3], $serialized);
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::REGISTER
            && $ast->getChilds()[2]->getValue()->getType() === NodeInterface::REGISTER
        ) {
            $this->processBinaryMovbRegsToRegsInstruction($ast, $result);
            return;
        }
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processBinaryMovbRegsToRegsInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::MOVB_R0_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::MOVB_R1_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::MOVB_R2_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::MOVB_R3_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::MOVB_R0_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::MOVB_R1_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::MOVB_R2_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::MOVB_R3_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::MOVB_R0_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::MOVB_R1_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::MOVB_R2_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::MOVB_R3_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result[] = Opcode::MOVB_R0_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result[] = Opcode::MOVB_R1_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result[] = Opcode::MOVB_R2_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result[] = Opcode::MOVB_R3_TO_R3;
            return;
        }
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processBinaryAddbInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::NUMBER) {
            $number = $ast->getChilds()[1]
                ->getValue()
                ->getValue();
            $serialized = $this->serializeNumberIntoDwordList($number);
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result = array_merge($result, [Opcode::ADDB_IMM8_TO_R0], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result = array_merge($result, [Opcode::ADDB_IMM8_TO_R1], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result = array_merge($result, [Opcode::ADDB_IMM8_TO_R2], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result = array_merge($result, [Opcode::ADDB_IMM8_TO_R3], $serialized);
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::REGISTER
            && $ast->getChilds()[2]->getValue()->getType() === NodeInterface::REGISTER
        ) {
            $this->processBinaryAddbRegsToRegsInstruction($ast, $result);
            return;
        }
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processBinaryAddbRegsToRegsInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::ADDB_R0_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::ADDB_R1_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::ADDB_R2_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::ADDB_R3_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::ADDB_R0_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::ADDB_R1_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::ADDB_R2_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::ADDB_R3_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::ADDB_R0_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::ADDB_R1_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::ADDB_R2_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::ADDB_R3_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result[] = Opcode::ADDB_R0_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result[] = Opcode::ADDB_R1_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result[] = Opcode::ADDB_R2_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result[] = Opcode::ADDB_R3_TO_R3;
            return;
        }
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processBinarySubbInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::NUMBER) {
            $number = $ast->getChilds()[1]
                ->getValue()
                ->getValue();
            $serialized = $this->serializeNumberIntoDwordList($number);
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result = array_merge($result, [Opcode::SUBB_IMM8_TO_R0], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result = array_merge($result, [Opcode::SUBB_IMM8_TO_R1], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result = array_merge($result, [Opcode::SUBB_IMM8_TO_R2], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result = array_merge($result, [Opcode::SUBB_IMM8_TO_R3], $serialized);
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::REGISTER
            && $ast->getChilds()[2]->getValue()->getType() === NodeInterface::REGISTER
        ) {
            $this->processBinarySubbRegsToRegsInstruction($ast, $result);
            return;
        }
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processBinarySubbRegsToRegsInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::SUBB_R0_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::SUBB_R1_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::SUBB_R2_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result[] = Opcode::SUBB_R3_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::SUBB_R0_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::SUBB_R1_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::SUBB_R2_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result[] = Opcode::SUBB_R3_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::SUBB_R0_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::SUBB_R1_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::SUBB_R2_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3"
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result[] = Opcode::SUBB_R3_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::SUBB_R0_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::SUBB_R1_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::SUBB_R2_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::SUBB_R3_TO_R3;
            return;
        }
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processBinaryMulbInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::NUMBER) {
            $number = $ast->getChilds()[1]
                ->getValue()
                ->getValue();
            $serialized = $this->serializeNumberIntoDwordList($number);
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result = array_merge($result, [Opcode::MULB_IMM8_TO_R0], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result = array_merge($result, [Opcode::MULB_IMM8_TO_R1], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result = array_merge($result, [Opcode::MULB_IMM8_TO_R2], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result = array_merge($result, [Opcode::MULB_IMM8_TO_R3], $serialized);
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::REGISTER &&
            $ast->getChilds()[2]->getValue()->getType() === NodeInterface::REGISTER) {
            $this->processBinaryMulbRegsToRegsInstruction($ast, $result);
            return;
        }
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processBinaryMulbRegsToRegsInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::MULB_R0_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::MULB_R1_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::MULB_R2_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::MULB_R3_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::MULB_R0_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::MULB_R1_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::MULB_R2_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::MULB_R3_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::MULB_R0_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::MULB_R1_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::MULB_R2_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::MULB_R3_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::MULB_R0_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::MULB_R1_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::MULB_R2_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::MULB_R3_TO_R3;
            return;
        }
    }

    /**
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processBinaryDivbInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::NUMBER) {
            $number = $ast->getChilds()[1]
                ->getValue()
                ->getValue();
            $serialized = $this->serializeNumberIntoDwordList($number);
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r0"
        ) {
            $result = array_merge($result, [Opcode::DIVB_IMM8_TO_R0], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r1"
        ) {
            $result = array_merge($result, [Opcode::DIVB_IMM8_TO_R1], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r2"
        ) {
            $result = array_merge($result, [Opcode::DIVB_IMM8_TO_R2], $serialized);
            return;
        }

        if (isset($serialized)
            && $ast->getChilds()[2]->getValue()->getValue() === "r3"
        ) {
            $result = array_merge($result, [Opcode::DIVB_IMM8_TO_R3], $serialized);
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::REGISTER &&
            $ast->getChilds()[2]->getValue()->getType() === NodeInterface::REGISTER) {
            $this->processBinaryDivbRegsToRegsInstruction($ast, $result);
            return;
        }
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processBinaryCmpbInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::NUMBER) {
            $number = $ast->getChilds()[1]
                ->getValue()
                ->getValue();
            $serialized = $this->serializeNumberIntoDwordList($number);
        }

        if (isset($serialized) &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result = array_merge($result, [Opcode::CMPB_IMM8_TO_R0], $serialized);
            return;
        }

        if (isset($serialized) &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result = array_merge($result, [Opcode::CMPB_IMM8_TO_R1], $serialized);
            return;
        }

        if (isset($serialized) &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result = array_merge($result, [Opcode::CMPB_IMM8_TO_R2], $serialized);
            return;
        }

        if (isset($serialized) &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result = array_merge($result, [Opcode::CMPB_IMM8_TO_R3], $serialized);
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::REGISTER &&
            $ast->getChilds()[2]->getValue()->getType() === NodeInterface::REGISTER) {
            $this->processBinaryCmpbRegsToRegsInstruction($ast, $result);
            return;
        }
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processBinaryBitwiseOrbInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::NUMBER) {
            $number = $ast->getChilds()[1]
                ->getValue()
                ->getValue();
            $serialized = $this->serializeNumberIntoDwordList($number);
        }

        if (isset($serialized) &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_IMM8_TO_R0],
                $serialized
            );

            return;
        }

        if (isset($serialized) &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_IMM8_TO_R1],
                $serialized
            );

            return;
        }

        if (isset($serialized) &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_IMM8_TO_R2],
                $serialized
            );

            return;
        }

        if (isset($serialized) &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_IMM8_TO_R3],
                $serialized
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getType() === NodeInterface::REGISTER &&
            $ast->getChilds()[2]->getValue()->getType() === NodeInterface::REGISTER) {
            $this->processBinaryBitwiseOrbRegsToRegsInstruction($ast, $result);
            return;
        }
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processBinaryDivbRegsToRegsInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::DIVB_R0_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::DIVB_R1_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::DIVB_R2_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::DIVB_R3_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::DIVB_R0_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::DIVB_R1_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::DIVB_R2_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::DIVB_R3_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::DIVB_R0_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::DIVB_R1_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::DIVB_R2_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::DIVB_R3_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::DIVB_R0_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::DIVB_R1_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::DIVB_R2_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::DIVB_R3_TO_R3;
            return;
        }
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processBinaryCmpbRegsToRegsInstruction(AstInterface $ast, array &$result)
    {
        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[1]->getValue()->getValue() === "r0") {
            $result[] = Opcode::CMPB_R0_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::CMPB_R1_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::CMPB_R2_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result[] = Opcode::CMPB_R3_TO_R0;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::CMPB_R0_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::CMPB_R1_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::CMPB_R2_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result[] = Opcode::CMPB_R3_TO_R1;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::CMPB_R0_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::CMPB_R1_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::CMPB_R2_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result[] = Opcode::CMPB_R3_TO_R2;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::CMPB_R0_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::CMPB_R1_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::CMPB_R2_TO_R3;
            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result[] = Opcode::CMPB_R3_TO_R3;
            return;
        }
    }

    /**
     * @param \Vm\AstInterface $ast
     * @param array &$result
     * @return void
     */
    private function processBinaryBitwiseOrbRegsToRegsInstruction(
        AstInterface $ast,
        array &$result
    ) {
        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R0_TO_R0]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R1_TO_R0]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R2_TO_R0]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r0") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R3_TO_R0]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R0_TO_R1]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R1_TO_R1]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R2_TO_R1]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r1") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R3_TO_R1]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R0_TO_R2]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R1_TO_R2]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R2_TO_R2]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r2") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R3_TO_R2]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r0" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R0_TO_R3]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r1" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R1_TO_R3]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r2" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R2_TO_R3]
            );

            return;
        }

        if ($ast->getChilds()[1]->getValue()->getValue() === "r3" &&
            $ast->getChilds()[2]->getValue()->getValue() === "r3") {
            $result = array_merge(
                $result,
                [Opcode::BITWISE_REX_CALL_GATE_PREFIX, BitwiseOpcode::ORB_R3_TO_R3]
            );

            return;
        }
    }

    /**
     * @param int $number
     * @return array
     */
    private function serializeVanillaNumberIntoDwordList(int $number): array
    {
        return [
            (($number & 0xff000000) >> 24),
            (($number & 0x00ff0000) >> 16),
            (($number & 0x0000ff00) >>  8),
            (($number & 0x000000ff) >>  0)
        ];
    }

    /**
     * @param  int $number
     * @return array
     */
    private function serializeNumberIntoDwordList(int $number): array
    {
        $normalized = abs($number);

        return [
            ($number < 0 ? 0xff : 0xfe),
            (($normalized & 0xff000000) >> 24),
            (($normalized & 0x00ff0000) >> 16),
            (($normalized & 0x0000ff00) >>  8),
            (($normalized & 0x000000ff) >>  0)
        ];
    }
}
