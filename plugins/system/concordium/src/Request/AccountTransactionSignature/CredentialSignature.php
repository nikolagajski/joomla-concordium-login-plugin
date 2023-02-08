<?php
/**
 * @package     Aesirx\Concordium
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       __DEPLOY_VERSION__
 */

namespace Aesirx\Concordium\Request\AccountTransactionSignature;

class CredentialSignature implements \Countable
{
	protected $data = [];

	public function __construct($array = [])
	{
		$this->data = $array;
	}

	/**
	 * @return string[]
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 *
	 * @return int
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function count(): int
	{
		return count($this->data);
	}
}