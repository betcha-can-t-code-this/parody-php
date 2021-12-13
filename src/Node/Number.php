<?php

declare(strict_types=1);

namespace Vm\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Number extends AbstractNode
{
	/**
	 * {@inheritdoc}
	 */
	public function getName(): string
	{
		return '<number>';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType(): int
	{
		return NodeInterface::NUMBER;
	}
}
