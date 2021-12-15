<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface CodegenInterface
{
    /**
     * @param  \Vm\AstInterface $ast
     * @return void
     */
    public function generate(AstInterface $ast);

    /**
     * @return \Vm\JumpLabelInterface
     */
    public function getJumpLabel(): JumpLabelInterface;
}
