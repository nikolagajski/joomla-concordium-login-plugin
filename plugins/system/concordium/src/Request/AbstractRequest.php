<?php
/**
 * @package     Aesirx\Concordium\Request
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       __DEPLOY_VERSION__
 */

namespace Aesirx\Concordium\Request;

/**
 * @package     Aesirx\Concordium\Request
 *
 * @since       __DEPLOY_VERSION__
 */
abstract class AbstractRequest
{
	/**
	 * @var array
	 * @since __DEPLOY_VERSION__
	 */
	protected $data = [];

	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed|null
	 *
	 * @since __DEPLOY_VERSION__
	 */
	public function __get(string $key)
	{
		if (array_key_exists($key, $this->data))
		{
			return $this->data[$key];
		}

		return null;
	}
}