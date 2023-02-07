<?php
/**
 * @package     Aesirx\Concordium\Extension\Concordium
 *
 * @copyright   Copyright (C) 2016 - 2023 Aesir. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       __DEPLOY_VERSION__
 */

namespace Aesirx\Concordium\Extension;

use Aesirx\Concordium\Table\NonceTable;
use Concordium\P2PClient;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Authentication\Authentication;
use Joomla\CMS\Authentication\AuthenticationResponse;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Event\CoreEventAware;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
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
use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Plugin\System\Webauthn\PluginTraits\EventReturnAware;
use StephenHill\Base58;

defined('_JEXEC') or die;

/**
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

	public function onUserAfterLogout($options)
	{

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

		$this->returnFromEvent($event, [
			[
				'label'              => 'PLG_SYSTEM_CONCORDIUM_LOGIN_LABEL',
				'tooltip'            => 'PLG_SYSTEM_CONCORDIUM_LOGIN_DESC',
				'id'                 => $randomId,
				'data-webauthn-form' => $form,
				'image'              => 'plg_system_concordium/concordium.png',
				'class'              => 'plg_system_concordium_login_button',
			],
		]);
	}

	/**
	 * Injects the WebAuthn CSS and Javascript for frontend logins, but only once per page load.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
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

		if (!$wa->assetExists('style', 'plg_system_concordium.button'))
		{
			$wa->registerStyle('plg_system_concordium.button', 'plg_system_concordium/button.css');
		}

		if (!$wa->assetExists('script', 'plg_system_concordium.login'))
		{
			$wa->registerScript('plg_system_concordium.login', 'plg_system_concordium/login.js', [], ['defer' => true], ['core']);
		}

		$wa->useStyle('plg_system_concordium.button')
			->useScript('plg_system_concordium.login');

//		/** @var SiteApplication $app */
//		$app = $this->getApplication();
//
//		$app->getDocument()->addScriptOptions('uri-root', Uri::root());
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
			throw new \Exception('Permission denied');
		}

		require_once JPATH_PLUGINS . '/system/concordium/vendor/autoload.php';
		header('Content-Type: application/json; charset=utf-8');

		$result = [];

		try
		{
			switch ($input->getString('task'))
			{
				case 'nonce':
					$nonceTable     = new NonceTable($this->getDatabase());
					$accountAddress = $input->getString('accountAddress');
					$save           = false;
					$now            = new Date;

					if ($nonceTable->load(['account_address' => $accountAddress]))
					{
						$createdAt  = new Date($nonceTable->get('created_at'));
						$expiryDate = clone $createdAt;
						$expiryDate->add(
							new \DateInterval($this->params->get('nonce_expired', 'PT10M'))
						);

						if ($now > $expiryDate)
						{
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

						if (!$nonceTable->save(
							[
								'nonce'           => $nonce,
								'account_address' => $accountAddress,
								'created_at'      => $now->toSql(),
							]
						))
						{
							throw new \Exception($nonceTable->getError());
						}
					}

					$result = [
						'nonce' => $this->getNonceMessage($nonce),
					];
					break;
				case 'auth':
					$accountAddress = $input->post->getString('accountAddress', '');
					$signed         = $input->post->get('signed', [], 'array');

					$nonceTable = new NonceTable($this->getDatabase());

					if (!$nonceTable->load(['account_address' => $accountAddress]))
					{
						throw new \Exception('Account not found');
					}

					$client = new P2PClient(
						$this->params->get('hostname'),
						[
							'credentials'     => \Grpc\ChannelCredentials::createInsecure(),
							'update_metadata' => function (array $metadata): array {
								$metadata['authentication'] = ['rpcadmin'];

								return $metadata;
							}
						]
					);
					/** @var \Concordium\JsonResponse $res */
					list($res) = $client->GetConsensusStatus(new \Concordium\PBEmpty)->wait();

					if (!$res || $res->getValue() == 'null')
					{
						throw new \Exception('Empty result');
					}

					$status = json_decode($res->getValue(), true);

					/** @var \Concordium\JsonResponse $res3 */
					list($res) = $client->GetAccountInfo(
						(new \Concordium\GetAddressInfoRequest)
							->setAddress($accountAddress)
							->setBlockHash($status['lastFinalizedBlock'])
					)->wait();

					if (!$res || $res->getValue() == 'null')
					{
						throw new \Exception('Empty result');
					}

					$res = $this->validate(
						$this->getNonceMessage($nonceTable->get('nonce')),
						$signed, json_decode($res->getValue(), true)
					);

					if (!$res)
					{
						throw new \Exception('Validation is failed');
					}

					$app->getSession()->set('plg_system_concordium.auth', true);

					$instance = new User;

					if ($nonceTable->get('user_id'))
					{
						if (!$instance->load($nonceTable->get('user_id')))
						{
							throw new \Exception('User not found');
						}

						$response           = new AuthenticationResponse;
						$response->status   = Authentication::STATUS_SUCCESS;
						$response->type     = 'Concordium';
						$response->username = $instance->username;
						$response->language = $instance->getParam('language');

						$options = [
							'remember'  => true,
							'entry_url' => Uri::base() . 'index.php?option=com_users&task=user.login',
							'action'    => 'core.login.site',
						];

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
							$options['responseType'] = $response['type'];

							// The user is successfully logged in. Run the after login events
							$eventClassName = self::getEventClassByEventName('onUserAfterLogin');
							$event          = new $eventClassName('onUserAfterLogin', [$options]);
							$app->getDispatcher()->dispatch($event->getName(), $event);
						}
						else
						{
							// If we are here the plugins marked a login failure. Trigger the onUserLoginFailure Event.
							$eventClassName = self::getEventClassByEventName('onUserLoginFailure');
							$event          = new $eventClassName('onUserLoginFailure', [$response]);
							$app->getDispatcher()->dispatch($event->getName(), $event);

							throw new \Exception();
						}

						$result = [
							'redirect' => Route::_($app->getUserState('users.login.form.return'), false),
						];
					}
					else
					{
						if (ComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0)
						{
							throw new \Exception('Registration is not allowed');
						}

						$app->enqueueMessage('Please fill in required fields to finish registration');

						$result = [
							'redirect' => Route::_('index.php?option=com_users&task=registration.register', false),
						];
					}
					break;
			}
		}
		catch (\Throwable $e)
		{
			Log::add(sprintf("Error: %s", $e->getMessage()), Log::ERROR, 'concordium.system');
			http_response_code(500);
			echo new JsonResponse($e);

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
		if (!$this->getApplication()->getSession()->get('plg_system_concordium.auth', false))
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
		$form->setFieldAttribute('password1', 'required', 'false');
		$form->setFieldAttribute('password2', 'required', 'false');
	}

	/**
	 * @param Event $event Event
	 *
	 * @return void
	 * @since __DEPLOY_VERSION__
	 */
	public function onUserAfterSave(Event $event): void
	{
		if (!$this->getApplication()->getSession()->get('plg_system_concordium.auth', false))
		{
			return;
		}

		//list($getProperties, $isNew, $result, $error) = $event->getArguments();
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
	 * @param string $message     Message
	 * @param array  $signatures  Signatures
	 * @param array  $accountInfo Account info
	 *
	 * @return boolean
	 *
	 * @throws \SodiumException
	 * @since __DEPLOY_VERSION__
	 */
	protected function validate(string $message, array $signatures, array $accountInfo): bool
	{
		if (count($signatures) < $accountInfo['accountThreshold'])
		{
			// Not enough credentials have signed;
			return false;
		}

		$base58 = new Base58;

		$res = substr(
			substr(
				$base58->decode($accountInfo['accountAddress']),
				1
			),
			0,
			-4
		);
		$i   = 0;

		while (true)
		{
			if ($i >= 8)
			{
				break;
			}

			$res .= chr(0);
			$i++;
		}

		$res .= $message;

		$hash = hash('sha256', $res);

		foreach ($signatures as $idx => $credentialSignature)
		{
			$credential = $accountInfo['accountCredentials'][$idx];

			if (!$credential)
			{
				throw new \Exception('Signature contains signature for non-existing credential');
			}

			$credentialKeys = $credential['value']['contents']['credentialPublicKeys'];

			if (count($credentialSignature) < $credentialKeys['threshold'])
			{
				// Not enough signatures for the current credential;
				return false;
			}

			foreach ($credentialSignature as $keyIndex => $signature)
			{
				if (!array_key_exists($keyIndex, $credentialKeys['keys']))
				{
					throw new \Exception('Signature contains signature for non-existing keyIndex');
				}

				if (!sodium_crypto_sign_verify_detached(
					hex2bin($signature),
					hex2bin($hash),
					hex2bin($credentialKeys['keys'][$keyIndex]['verifyKey'])))
				{
					// Incorrect signature;
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		try
		{
			$app = Factory::getApplication();
		}
		catch (\Exception $e)
		{
			return [];
		}

		if (!$app->isClient('site'))
		{
			return [];
		}

		return [
			'onUserLoginButtons'   => 'onUserLoginButtons',
			'onUserAfterLogout'    => 'onUserAfterLogout',
			'onContentPrepareForm' => 'onContentPrepareForm',
			'onAfterRoute'         => 'onAfterRoute',
			'onUserAfterSave'      => 'onUserAfterSave',
		];
	}
}
