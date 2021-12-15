<?php

declare(strict_types=1);

namespace Vm;

/**
 * @author Paulus Gandung Prakosa <gandung@lists.infradead.org>
 */
interface JumpLabelInterface
{
	/**
	 * @param string $label
	 * @param int $ip
	 * @return void
	 */
	public function add(string $label, int $ip);

	/**
	 * @param string $label
	 * @return int|null
	 */
	public function fetch(string $label): ?int;

	/**
	 * @return array
	 */
	public function getMap(): array;
}
