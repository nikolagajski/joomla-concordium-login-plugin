<?php
/**
 * @package     Aesirx\Concordium
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       __DEPLOY_VERSION__
 */

namespace Aesirx\Concordium\Request\AccountInfo;

use Aesirx\Concordium\Request\AbstractRequest;

/**
 * @property int $accountThreshold
 * @property string $accountAddress
 * @property AccountCredential[] $accountCredentials
 *
 * @package     Aesirx\Concordium\Request\AccountInfo
 *
 * @since       __DEPLOY_VERSION__
 */
class AccountInfo extends AbstractRequest
{
	public function __construct(array $data)
	{
		parent::__construct($data);
		$this->data['accountCredentials'] = [];

		foreach ($data['accountCredentials'] as $key => $val)
		{
			$this->data['accountCredentials'][$key] = new AccountCredential($val);
		}
	}
}