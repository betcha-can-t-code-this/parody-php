<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
final class JumpLabel implements JumpLabelInterface
{
    /**
     * @var array
     */
    private $labelMap = [];

    /**
     * {@inheritdoc}
     */
    public function add(string $label, int $ip)
    {
        $this->labelMap[$label] = $ip;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(string $label): int
    {
        return !isset($this->labelMap[$label])
            ? -1
            : $this->labelMap[$label];
    }

    /**
     * {@inheritdoc}
     */
    public function getMap(): array
    {
        return $this->labelMap;
    }
}
