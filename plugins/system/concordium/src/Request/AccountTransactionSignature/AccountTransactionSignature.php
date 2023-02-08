<?php
/**
 * @package     Aesirx\Concordium
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       __DEPLOY_VERSION__
 */

namespace Aesirx\Concordium\Request\AccountTransactionSignature;

class AccountTransactionSignature implements \Countable
{
	protected $data = [];

	public function __construct(array $data)
	{
		foreach ($data as $key => $values)
		{
			$this->data[$key] = new CredentialSignature($values);
		}
	}

	/**
	 * @return CredentialSignature[]
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function getData():array
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