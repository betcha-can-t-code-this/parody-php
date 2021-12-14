<?php

declare(strict_types=1);

namespace Vm;

use Vm\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Codegen implements CodegenInterface
{
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

        foreach ($ast->getChilds() as $vnode) {
            if ($vnode->getType() !== AstInterface::AST_INSTRUCTION_LINE) {
                $gotException = true;
                break;
            }

            $this->processInstructionLine($vnode, $tmp);
        }

        if ($gotException) {
            throw new AstException(
                "Current ast node is not instruction line."
            );
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
     * @param  \Vm\AstInterface $ast
     * @param  array            &$result
     * @return void
     */
    private function processInstructionLine(AstInterface $ast, array &$result)
    {
        if (sizeof($ast->getChilds()) === 2 
            && $ast->getChilds()[0]->getValue()->getValue() === "prib"
        ) {
            $this->processUnaryPribInstruction($ast, $result);
        }

        if (sizeof($ast->getChilds()) === 3 
            && $ast->getChilds()[0]->getValue()->getValue() === "movb"
        ) {
            $this->processBinaryMovbInstruction($ast, $result);
        }

        if (sizeof($ast->getChilds()) === 3 
            && $ast->getChilds()[0]->getValue()->getValue() === "addb"
        ) {
            $this->processBinaryAddbInstruction($ast, $result);
        }

        if (sizeof($ast->getChilds()) === 3 
            && $ast->getChilds()[0]->getValue()->getValue() === "subb"
        ) {
            $this->processBinarySubbInstruction($ast, $result);
        }

        if (sizeof($ast->getChilds()) === 3 
            && $ast->getChilds()[0]->getValue()->getValue() === "mulb"
        ) {
            $this->processBinaryMulbInstruction($ast, $result);
        }

        if (sizeof($ast->getChilds()) === 3 
            && $ast->getChilds()[0]->getValue()->getValue() === "divb"
        ) {
            $this->processBinaryDivbInstruction($ast, $result);
        }
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
