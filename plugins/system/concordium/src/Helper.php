<?php
/**
 * @package     Aesirx\Concordium
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       __DEPLOY_VERSION__
 */

namespace Aesirx\Concordium;

use Aesirx\Concordium\Request\AccountInfo\AccountInfo;
use Aesirx\Concordium\Request\AccountTransactionSignature\AccountTransactionSignature;
use Exception;
use StephenHill\Base58;

/**
 * @package     Aesirx\Concordium
 *
 * @since       __DEPLOY_VERSION__
 */
class Helper
{
	/**
	 * @param string                      $message
	 * @param AccountTransactionSignature $signatures
	 * @param AccountInfo                 $accountInfo
	 *
	 * @return bool
	 *
	 * @throws \SodiumException
	 * @since __DEPLOY_VERSION__
	 */
	public static function verifyMessageSignature(
		string                      $message,
		AccountTransactionSignature $signatures,
		AccountInfo                 $accountInfo): bool
	{
		if (count($signatures) < $accountInfo->accountThreshold)
		{
			// Not enough credentials have signed
			return false;
		}

		$hash = hash(
			'sha256',
			substr(substr((new Base58)->decode($accountInfo->accountAddress), 1), 0, -4)
			. str_repeat(chr(0), 8)
			. $message
		);

		foreach ($signatures->getData() as $idx => $credentialSignature)
		{
			$credential = $accountInfo->accountCredentials[$idx];

			if (!$credential)
			{
				throw new Exception('Signature contains signature for non-existing credential');
			}

			$credentialKeys = $credential->value->contents->credentialPublicKeys;

			if (count($credentialSignature) < $credentialKeys->threshold)
			{
				// Not enough signatures for the current credential
				return false;
			}

			foreach ($credentialSignature->getData() as $keyIndex => $signature)
			{
				if (!array_key_exists($keyIndex, $credentialKeys->keys))
				{
					throw new Exception('Signature contains signature for non-existing keyIndex');
				}

				if (!sodium_crypto_sign_verify_detached(
					hex2bin($signature),
					hex2bin($hash),
					hex2bin($credentialKeys->keys[$keyIndex]->verifyKey)))
				{
					// Incorrect signature
					return false;
				}
			}
		}

		return true;
	}
}