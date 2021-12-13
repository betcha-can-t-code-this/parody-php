<?php

declare(strict_types=1);

namespace Vm\Node;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface NodeInterface
{
	/**
	 * @var int
	 */
	const COMMA = 0;

	/**
	 * @var int
	 */
	const MNEMONIC = 1;

	/**
	 * @var int
	 */
	const NEWLINE = 2;

	/**
	 * @var int
	 */
	const NUMBER = 3;

	/**
	 * @var int
	 */
	const REGISTER = 4;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @return mixed
	 */
	public function getValue();

	/**
	 * @return int
	 */
	public function getType(): int;
}
