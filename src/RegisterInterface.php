<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface RegisterInterface
{
    /**
     * @return int
     */
    public function getR0(): int;

    /**
     * @param  int $r0
     * @return void
     */
    public function setR0(int $r0);

    /**
     * @return int
     */
    public function getR1(): int;

    /**
     * @param  int $r1
     * @return void
     */
    public function setR1(int $r1);

    /**
     * @return int
     */
    public function getR2(): int;

    /**
     * @param  int $r2
     * @return void
     */
    public function setR2(int $r2);

    /**
     * @return int
     */
    public function getR3(): int;

    /**
     * @param  int $r3
     * @return void
     */
    public function setR3(int $r3);
}
