<?php
/**
 * @package     Aesirx\Concordium\Table
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Aesirx\Concordium\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

defined('_JEXEC') or die;

/**
 * @package     Aesirx\Concordium\Table
 *
 * @since       __DEPLOY_VERSION__
 */
class NonceTable extends Table
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function __construct(DatabaseDriver $db)
	{
		parent::__construct('#__concordium_nonce', 'account_address', $db);

		$this->_autoincrement = false;
	}
}