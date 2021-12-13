<?php

declare(strict_types=1);

namespace Vm\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
abstract class AbstractNode implements NodeInterface
{
	/**
	 * @var mixed
	 */
	private $value;

	/**
	 * @var \Vm\Node\NodeInterface[]
	 */
	private $childs = [];

	/**
	 * @param mixed $value
	 * @return static
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	abstract public function getName(): string;

	/**
	 * {@inheritdoc}
	 */
	abstract public function getType(): int;

	/**
	 * {@inheritdoc}
	 */
	public function getValue()
	{
		return $this->value;
	}
}
