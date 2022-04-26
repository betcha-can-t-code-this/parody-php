<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface RuntimeInterface
{
    /**
     * @return void
     */
    public function run();

    /**
     * @return \Vm\EflagsInterface
     */
    public function getEflags(): EflagsInterface;

    /**
     * @param \Vm\EflagsInterface $eflags
     * @return void
     */
    public function setEflags(EflagsInterface $eflags);

    /**
     * @return \Vm\RegisterInterface
     */
    public function getRegister(): RegisterInterface;

    /**
     * @param  \Vm\RegisterInterface $register
     * @return void
     */
    public function setRegister(RegisterInterface $register);
}
