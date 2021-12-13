<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
class Register implements RegisterInterface
{
	/**
	 * @return int
	 */
	private $r0;

	/**
	 * @return int
	 */
	private $r1;

	/**
	 * @return int
	 */
	private $r2;

	/**
	 * @return int
	 */
	private $r3;

	/**
	 * @return static
	 */
	public function __construct()
	{
		$this->setR0(0);
		$this->setR1(0);
		$this->setR2(0);
		$this->setR3(0);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getR0(): int
	{
		return $this->r0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setR0(int $r0)
	{
		$this->r0 = $r0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getR1(): int
	{
		return $this->r1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setR1(int $r1)
	{
		$this->r1 = $r1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getR2(): int
	{
		return $this->r2;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setR2(int $r2)
	{
		$this->r2 = $r2;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getR3(): int
	{
		return $this->r3;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setR3(int $r3)
	{
		$this->r3 = $r3;
	}
}
