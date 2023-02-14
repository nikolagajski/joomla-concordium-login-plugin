<?php
/**
 * @package     Aesirx\Concordium\Extension\Concordium
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       __DEPLOY_VERSION__
 */

namespace Aesirx\Concordium\Extension;

use Aesirx\Concordium\Exception\ResponseException;
use Aesirx\Concordium\Helper;
use Aesirx\Concordium\Request\AccountInfo\AccountInfo;
use Aesirx\Concordium\Request\AccountTransactionSignature\AccountTransactionSignature;
use Aesirx\Concordium\Table\NonceTable;
use Concordium\P2PClient;
use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\Authentication\AuthenticationResponse;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Event\CoreEventAware;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Plugin\System\Webauthn\PluginTraits\EventReturnAware;

defined('_JEXEC') or die;

/**
 * @method CMSApplication getApplication()
 *
 * Class Concordium
 * @package Aesirx\Concordium
 *
 * @since   __DEPLOY_VERSION__
 */
class Concordium extends CMSPlugin implements SubscriberInterface
{
	use EventReturnAware, DatabaseAwareTrait, CoreEventAware;

	protected $autoloadLanguage = true;

	/**
	 * Have I already injected CSS and JavaScript? Prevents double inclusion of the same files.
	 *
	 * @var     boolean
	 * @since   __DEPLOY_VERSION__
	 */
	private $injectedCSSandJS = false;

	/**
	 * Constructor
	 *
	 * @param DispatcherInterface $subject     The object to observe
	 * @param array               $config      An optional associative array of configuration settings.
	 *                                         Recognized key values include 'name', 'group', 'params', 'language'
	 *                                         (this list is not meant to be comprehensive).
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Register a debug log file writer
		$logLevels = Log::ERROR | Log::CRITICAL | Log::ALERT | Log::EMERGENCY;

		if (\defined('JDEBUG') && JDEBUG)
		{
			$logLevels = Log::ALL;
		}

		Log::addLogger([
			'text_file'         => "concordium_system.php",
			'text_entry_format' => '{DATETIME}	{PRIORITY} {CLIENTIP}	{MESSAGE}',
		], $logLevels, ["concordium.system"]);
	}

	/**
	 * Creates additional login buttons
	 *
	 * @param Event $event The event we are handling
	 *
	 * @return  void
	 *
	 * @see     AuthenticationHelper::getLoginButtons()
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function onUserLoginButtons(Event $event): void
	{
		/** @var string $form The HTML ID of the form we are enclosed in */
		[$form] = $event->getArguments();

		// Load necessary CSS and Javascript files
		$this->addLoginCSSAndJavascript();

		// Unique ID for this button (allows display of multiple modules on the page)
		$randomId = 'plg_system_concordium-' .
			UserHelper::genRandomPassword(12) . '-' . UserHelper::genRandomPassword(8);

		// Get local path to image
		$image = HTMLHelper::_('image', 'plg_system_concordium/concordium_black.svg', '', '', true, true);

		// If you can't find the image then skip it
		$image = $image ? JPATH_ROOT . substr($image, \strlen(Uri::root(true))) : '';

		// Extract image if it exists
		$image = file_exists($image) ? file_get_contents($image) : '';

		$this->returnFromEvent($event, [
			[
				'label'              => 'PLG_SYSTEM_CONCORDIUM_LOGIN_LABEL',
				'tooltip'            => 'PLG_SYSTEM_CONCORDIUM_LOGIN_DESC',
				'id'                 => $randomId,
				'data-webauthn-form' => $form,
				'svg'                => $image,
				'class'              => 'plg_system_concordium_login_button',
			],
		]);
	}

	/**
	 * Injects the WebAuthn CSS and Javascript for frontend logins, but only once per page load.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function addLoginCSSAndJavascript(): void
	{
		if ($this->injectedCSSandJS)
		{
			return;
		}

		// Set the "don't load again" flag
		$this->injectedCSSandJS = true;

		/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = $this->getApplication()->getDocument()->getWebAssetManager();

		if (!$wa->assetExists('script', 'plg_system_concordium.login'))
		{
			$wa->registerScript('plg_system_concordium.login', 'plg_system_concordium/login.js', [], ['defer' => true], ['core']);
		}

		$wa->useScript('plg_system_concordium.login');

		Text::script('PLG_SYSTEM_CONCORDIUM_LOGIN_LABEL');
		Text::script('PLG_SYSTEM_CONCORDIUM_APP_IS_NOT_INSTALLED');
		Text::script('PLG_SYSTEM_CONCORDIUM_CONNECTING');
		Text::script('PLG_SYSTEM_CONCORDIUM_CONNECTED');
		Text::script('PLG_SYSTEM_CONCORDIUM_SIGNING_NONCE');
		Text::script('PLG_SYSTEM_CONCORDIUM_WALLET_REJECT');
		Text::script('PLG_SYSTEM_CONCORDIUM_SIGNING_NONCE_SIGNED');
	}

	/**
	 * @return void
	 * @since __DEPLOY_VERSION__
	 */
	public function onAfterRoute(): void
	{
		/** @var SiteApplication $app */
		$app   = $this->getApplication();
		$input = $app->input;

		if ($input->getString('option') != 'concordium')
		{
			return;
		}

		if ($input->getMethod() !== 'POST')
		{
			throw new Exception('Permission denied');
		}

		require_once JPATH_PLUGINS . '/system/concordium/vendor/autoload.php';
		header('Content-Type: application/json; charset=utf-8');

		$result = [];

		$isFrontend = $app->isClient('site');

		try
		{
			switch ($input->getString('task'))
			{
				case 'nonce':
					$nonceTable     = new NonceTable($this->getDatabase());
					$accountAddress = $input->getString('accountAddress');
					$save           = false;
					$now            = new Date;
					Log::add('Calling nonce', Log::INFO, 'concordium.system');

					if ($nonceTable->load(['account_address' => $accountAddress]))
					{
						Log::add(sprintf('Found nonce record for %s', $accountAddress), Log::INFO, 'concordium.system');
						$createdAt  = new Date($nonceTable->get('created_at'));
						$expiryDate = clone $createdAt;
						$expiryDate->add(
							new \DateInterval($this->params->get('nonce_expired', 'PT10M'))
						);

						if ($now > $expiryDate)
						{
							Log::add(sprintf('Nonce expired for %s', $accountAddress), Log::INFO, 'concordium.system');
							$save = true;
						}
						else
						{
							$nonce = $nonceTable->get('nonce');
						}
					}
					else
					{
						$save = true;
					}

					if ($save)
					{
						$nonce = sprintf("%06d", rand(0, 999999));
						Log::add(sprintf('Saving nonce for %s', $accountAddress), Log::INFO, 'concordium.system');

						if (!$nonceTable->save(
							[
								'nonce'           => $nonce,
								'account_address' => $accountAddress,
								'created_at'      => $now->toSql(),
							]
						))
						{
							throw new Exception($nonceTable->getError());
						}
					}

					$result['nonce'] = $this->getNonceMessage($nonce);
					break;
				case 'auth':
					$accountAddress = $input->post->getString('accountAddress', '');
					$return         = base64_decode($input->post->get('return', '', 'BASE64'));
					$signed         = $input->post->get('signed', [], 'array');
					$remember       = $input->post->getBool('remember', false);

					Log::add(sprintf('Calling auth for %s', $accountAddress), Log::INFO, 'concordium.system');

					$nonceTable = new NonceTable($this->getDatabase());

					if (!$nonceTable->load(['account_address' => $accountAddress]))
					{
						throw new Exception(Text::_('PLG_SYSTEM_CONCORDIUM_CONCORDIUM_ACCOUNT_NOT_FOUND'));
					}

					$app->getSession()->set('plg_system_concordium.user_id', $nonceTable->get('user_id'));

					switch ($this->params->get('server_type'))
					{
						case 'JSON-RPC':
							$client = new \JsonRpc\Client($this->params->get('json_hostname'));

							if (!$client->call('getConsensusStatus', []))
							{
								throw new ResponseException($client->error, Text::_('PLG_SYSTEM_CONCORDIUM_CLIENT_RETURNS_NOTHING'));
							}

							if (!$client->call(
								'getAccountInfo', [
									'address' => $accountAddress,
									'blockHash' => $client->result->lastFinalizedBlock
								]
							))
							{
								throw new ResponseException($client->error, Text::_('PLG_SYSTEM_CONCORDIUM_CLIENT_RETURNS_NOTHING'));
							}

							$res = json_decode(json_encode($client->result), true);

							break;
						case 'gRPC':
							$client = new P2PClient(
								$this->params->get('g_hostname'),
								[
									'credentials'     => \Grpc\ChannelCredentials::createInsecure(),
									'update_metadata' => function (array $metadata): array {
										$metadata['authentication'] = ['rpcadmin'];

										return $metadata;
									}
								]
							);

							$opt = [
								// 3 seconds in milliseconds
								'timeout' => 3000000,
							];

							/** @var \Concordium\JsonResponse $res */
							list($res, $res2) = $client->GetConsensusStatus(new \Concordium\PBEmpty, [], $opt)->wait();

							if (!$res || $res->getValue() == 'null')
							{
								throw new ResponseException($res2, Text::_('PLG_SYSTEM_CONCORDIUM_CLIENT_RETURNS_NOTHING'));
							}

							Log::add('Got consensus status', Log::INFO, 'concordium.system');

							$status = json_decode($res->getValue(), true);

							/** @var \Concordium\JsonResponse $res3 */
							list($res, $res2) = $client->GetAccountInfo(
								(new \Concordium\GetAddressInfoRequest)
									->setAddress($accountAddress)
									->setBlockHash($status['lastFinalizedBlock']),
								[],
								$opt
							)->wait();

							if (!$res || $res->getValue() == 'null')
							{
								throw new ResponseException($res2, Text::_('PLG_SYSTEM_CONCORDIUM_CLIENT_RETURNS_NOTHING'));
							}

							$res = json_decode($res->getValue(), true);
							break;
						default:
							throw new Exception('Unknown RPC server type');
					}

					Log::add(sprintf('Got account info for %s', $accountAddress), Log::INFO, 'concordium.system');

					if (!Helper::verifyMessageSignature(
						$this->getNonceMessage($nonceTable->get('nonce')),
						new AccountTransactionSignature($signed),
						new AccountInfo($res)
					))
					{
						throw new Exception(Text::_('PLG_SYSTEM_CONCORDIUM_VALIDATION_IS_FAILED'));
					}

					$app->getSession()->set('plg_system_concordium.account_address', $accountAddress);

					if ($nonceTable->get('user_id'))
					{
						$instance = new User;

						if (!$instance->load($nonceTable->get('user_id')))
						{
							throw new Exception(Text::_('PLG_SYSTEM_CONCORDIUM_JOOMLA_ACCOUNT_NOT_FOUND'));
						}

						$response           = new AuthenticationResponse;
						$response->status   = Authentication::STATUS_SUCCESS;
						$response->type     = 'Concordium';
						$response->username = $instance->username;
						$response->language = $instance->getParam('language');

						if ($isFrontend)
						{
							$options = [
								'remember' => $remember,
								'action'   => 'core.login.site',
							];

							// Check for a simple menu item id
							if (is_numeric($return))
							{
								$itemId = (int) $return;
								$return = 'index.php?Itemid=' . $itemId;

								if (Multilanguage::isEnabled())
								{
									$db    = $this->getDatabase();
									$query = $db->getQuery(true)
										->select($db->quoteName('language'))
										->from($db->quoteName('#__menu'))
										->where($db->quoteName('client_id') . ' = 0')
										->where($db->quoteName('id') . ' = :id')
										->bind(':id', $itemId, ParameterType::INTEGER);

									$language = $db->setQuery($query)
										->loadResult();

									if ($language !== '*')
									{
										$return .= '&lang=' . $language;
									}
								}
							}
							elseif (!Uri::isInternal($return))
							{
								// Don't redirect to an external URL.
								$return = '';
							}

							// Set the return URL if empty.
							if (empty($return))
							{
								$return = 'index.php?option=com_users&view=profile';
							}

							// Set the return URL in the user state to allow modification by plugins
							$app->setUserState('users.login.form.return', $return);
						}
						else
						{
							$options = [
								'action'   => 'core.login.admin',
							];

							if (!Uri::isInternal($return)
								|| strpos($return, 'tmpl=component') !== false)
							{
								$return = 'index.php';
							}
						}

						PluginHelper::importPlugin('user');
						$eventClassName = self::getEventClassByEventName('onUserLogin');
						$event          = new $eventClassName('onUserLogin', [(array) $response, $options]);
						$dispatched     = $app->getDispatcher()->dispatch($event->getName(), $event);
						$results        = !isset($dispatched['result']) || \is_null($dispatched['result']) ? [] : $dispatched['result'];

						// If there is no boolean FALSE result from any plugin the login is successful.
						if (in_array(false, $results, true) === false)
						{
							// Set the user in the session, letting Joomla! know that we are logged in.
							$app->getSession()->set('user', $instance);

							// Trigger the onUserAfterLogin event
							$options['user']         = $instance;
							$options['responseType'] = $response->type;

							// The user is successfully logged in. Run the after login events
							$eventClassName = self::getEventClassByEventName('onUserAfterLogin');
							$event          = new $eventClassName('onUserAfterLogin', [$options]);
							$app->getDispatcher()->dispatch($event->getName(), $event);
						}
						else
						{
							// If we are here the plugins marked a login failure. Trigger the onUserLoginFailure Event.
							$eventClassName = self::getEventClassByEventName('onUserLoginFailure');
							$event          = new $eventClassName('onUserLoginFailure', [(array) $response]);
							$app->getDispatcher()->dispatch($event->getName(), $event);

							throw new Exception();
						}

						if ($isFrontend
							&& $options['remember'] == true)
						{
							$app->setUserState('rememberLogin', true);
						}

						Log::add(sprintf('Redirect %s to after-login page', $accountAddress), Log::INFO, 'concordium.system');

						if ($isFrontend)
						{
							$result['redirect'] = Route::_($app->getUserState('users.login.form.return'), false);
						}
						else
						{
							$result['redirect'] = $return;
						}
					}
					else
					{
						if (!$isFrontend
							|| ComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0)
						{
							throw new Exception(Text::_('PLG_SYSTEM_CONCORDIUM_ACCOUNT_NOT_FOUND'));
						}
						else
						{
							throw new Exception(Text::_('PLG_SYSTEM_CONCORDIUM_ACCOUNT_NOT_FOUND_REGISTRATION_ALLOWED'));
						}
					}
					break;
			}
		}
		catch (\Throwable $e)
		{
			Log::add(sprintf("Error: %s", $e->getMessage()), Log::ERROR, 'concordium.system');
			http_response_code(500);
			$resp = new JsonResponse($e);

			if ($this->getApplication()->get('debug'))
			{
				$resp->trace = $e->getTrace();

				if ($e instanceof ResponseException)
				{
					$resp->response = $e->getResponse();
				}
			}

			echo $resp;

			$app->close();
		}

		echo new JsonResponse($result);

		$app->close();
	}

	/**
	 * @param Event $event Event
	 *
	 * @return void
	 * @since __DEPLOY_VERSION__
	 */
	public function onContentPrepareForm(Event $event): void
	{
		if (!$this->getAccountAddress())
		{
			return;
		}

		/**  @var Form $form */
		list($form) = $event->getArguments();

		if ($form->getName() !== 'com_users.registration')
		{
			return;
		}

		$form->removeField('captcha');
	}

	/**
	 * @param Event $event Event
	 *
	 * @return void
	 * @since __DEPLOY_VERSION__
	 */
	public function onUserAfterSave(Event $event): void
	{
		$accountAddress = $this->getAccountAddress();

		if (!$accountAddress
			|| $this->getUserId())
		{
			return;
		}

		list($getProperties) = $event->getArguments();

		if (empty($getProperties['id']))
		{
			return;
		}

		$this->linkUserToAccountAddress($getProperties['id']);
	}

	/**
	 * @param string $nonce Nonce
	 *
	 * @return string
	 *
	 * @since __DEPLOY_VERSION__
	 */
	protected function getNonceMessage(string $nonce): string
	{
		return Text::sprintf('PLG_SYSTEM_CONCORDIUM_NONCE_MESSAGE', $nonce);
	}

	/**
	 * @since __DEPLOY_VERSION__
	 */
	public function onUserLogout(): void
	{
		if (!$this->getAccountAddress())
		{
			return;
		}

		$this->getApplication()->getSession()->set('plg_system_concordium.account_address');
		$this->getApplication()->getSession()->set('plg_system_concordium.user_id');
	}

	/**
	 * @param int $userId
	 *
	 *
	 * @throws Exception
	 * @since __DEPLOY_VERSION__
	 */
	protected function linkUserToAccountAddress(int $userId): void
	{
		$accountAddress = $this->getAccountAddress();

		if (!$accountAddress)
		{
			return;
		}

		$nonceTable = new NonceTable($this->getDatabase());

		if (!$nonceTable->load(['account_address' => $accountAddress]))
		{
			throw new Exception(Text::_('PLG_SYSTEM_CONCORDIUM_CONCORDIUM_ACCOUNT_NOT_FOUND'));
		}

		// Once user assigned then do not override it
		if ($nonceTable->get('user_id'))
		{
			return;
		}

		if (!$nonceTable->save(['user_id' => $userId]))
		{
			throw new Exception($nonceTable->getError());
		}

		$this->getApplication()->getSession()->set('plg_system_concordium.user_id', $userId);
		$this->getApplication()->enqueueMessage(Text::_('PLG_SYSTEM_CONCORDIUM_ACCOUNT_LINED'));
	}

	/**
	 * @param Event $event
	 *
	 *
	 * @throws Exception
	 * @since __DEPLOY_VERSION__
	 */
	public function onUserLogin(Event $event): void
	{
		$accountAddress = $this->getAccountAddress();

		if (!$accountAddress
			|| $this->getUserId())
		{
			return;
		}

		list($user) = $event->getArguments();

		if (empty($user['username']))
		{
			return;
		}

		$id = (int) UserHelper::getUserId($user['username']);

		if (!$id)
		{
			return;
		}

		$this->linkUserToAccountAddress($id);
	}

	/**
	 * @return null|string
	 *
	 * @since __DEPLOY_VERSION__
	 */
	protected function getAccountAddress(): ?string
	{
		return $this->getApplication()->getSession()->get('plg_system_concordium.account_address');
	}

	/**
	 * @return string|null
	 *
	 * @since __DEPLOY_VERSION__
	 */
	protected function getUserId(): ?string
	{
		return $this->getApplication()->getSession()->get('plg_system_concordium.user_id');
	}

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getSubscribedEvents(): array
	{
		try
		{
			$app = Factory::getApplication();
		}
		catch (Exception $e)
		{
			return [];
		}

		if (!in_array($app->getName(), ['site', 'administrator']))
		{
			return [];
		}

		return [
			'onUserLoginButtons'   => 'onUserLoginButtons',
			'onContentPrepareForm' => 'onContentPrepareForm',
			'onAfterRoute'         => 'onAfterRoute',
			'onUserAfterSave'      => 'onUserAfterSave',
			'onUserLogout'         => 'onUserLogout',
			'onUserLogin'          => 'onUserLogin',
		];
	}
}
