<?php

declare(strict_types=1);

namespace Vm;

use Vm\Node\NodeInterface;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Ast implements AstInterface
{
	/**
	 * @var \Vm\Node\NodeInterface
	 */
	private $value;

	/**
	 * @var int
	 */
	private $type;

	/**
	 * @var array
	 */
	private $childs = [];

	/**
	 * @param int $type
	 * @param \Vm\Node\NodeInterface|null $value
	 * @return static
	 */
	public function __construct(int $type, ?NodeInterface $value)
	{
		$this->setType($type);
		$this->setValue($value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getValue(): ?NodeInterface
	{
		return $this->value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setValue(?NodeInterface $value)
	{
		$this->value = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getType(): int
	{
		return $this->type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setType(int $type)
	{
		$this->type = $type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getChilds(): array
	{
		return $this->childs;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setChilds(array $childs)
	{
		$this->childs = $childs;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addChild(AstInterface $value)
	{
		$this->childs[] = $value;
	}
}
