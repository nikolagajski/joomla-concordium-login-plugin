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
 * @property int $v
 * @property AccountCredentialType $value
 *
 * @package     Aesirx\Concordium\Request\AccountInfo
 *
 * @since       __DEPLOY_VERSION__
 */
class AccountCredential extends AbstractRequest
{
	public function __construct(array $data)
	{
		parent::__construct($data);
		$this->data['value'] = new AccountCredentialType($data['value']);
	}
}