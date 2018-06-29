<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Joomlaupdate\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Fileystem\File;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Factory;

/**
 * The Joomla! update controller for the Update view
 *
 * @since  2.5.4
 */
class UpdateController extends BaseController
{
	/**
	 * Performs the download of the update package
	 *
	 * @return  void
	 *
	 * @since   2.5.4
	 */
	public function download()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
		$options['text_file'] = 'joomla_update.php';
		Log::addLogger($options, Log::INFO, array('Update', 'databasequery', 'jerror'));
		$user = Factory::getUser();

		try
		{
			Log::add(Text::sprintf('COM_JOOMLAUPDATE_UPDATE_LOG_START', $user->id, $user->name, \JVERSION), Log::INFO, 'Update');
		}
		catch (\RuntimeException $exception)
		{
			// Informational log only
		}

		$this->_applyCredentials();

		/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('Update');
		$file = $model->download();

		$message = null;
		$messageType = null;

		if ($file)
		{
			$this->app->setUserState('com_joomlaupdate.file', $file);
			$url = 'index.php?option=com_joomlaupdate&task=update.install&' . $this->app->getSession()->getFormToken() . '=1';

			try
			{
				Log::add(Text::sprintf('COM_JOOMLAUPDATE_UPDATE_LOG_FILE', $file), Log::INFO, 'Update');
			}
			catch (\RuntimeException $exception)
			{
				// Informational log only
			}
		}
		else
		{
			$this->app->setUserState('com_joomlaupdate.file', null);
			$url = 'index.php?option=com_joomlaupdate';
			$message = Text::_('COM_JOOMLAUPDATE_VIEW_UPDATE_DOWNLOADFAILED');
			$messageType = 'error';
		}

		$this->setRedirect($url, $message, $messageType);
	}

	/**
	 * Start the installation of the new Joomla! version
	 *
	 * @return  void
	 *
	 * @since   2.5.4
	 */
	public function install()
	{
		Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

		$options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
		$options['text_file'] = 'joomla_update.php';
		Log::addLogger($options, Log::INFO, array('Update', 'databasequery', 'jerror'));

		try
		{
			Log::add(Text::_('COM_JOOMLAUPDATE_UPDATE_LOG_INSTALL'), Log::INFO, 'Update');
		}
		catch (\RuntimeException $exception)
		{
			// Informational log only
		}

		$this->_applyCredentials();

		/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('Update');

		$file = Factory::getApplication()->getUserState('com_joomlaupdate.file', null);
		$model->createRestorationFile($file);

		$this->display();
	}

	/**
	 * Finalise the upgrade by running the necessary scripts
	 *
	 * @return  void
	 *
	 * @since   2.5.4
	 */
	public function finalise()
	{
		/*
		 * Finalize with login page. Used for pre-token check versions
		 * to allow updates without problems but with a maximum of security.
		 */
		if (!Session::checkToken('get'))
		{
			$this->setRedirect('index.php?option=com_joomlaupdate&view=update&layout=finaliseconfirm');

			return false;
		}

		$options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
		$options['text_file'] = 'joomla_update.php';
		Log::addLogger($options, Log::INFO, array('Update', 'databasequery', 'jerror'));

		try
		{
			Log::add(Text::_('COM_JOOMLAUPDATE_UPDATE_LOG_FINALISE'), Log::INFO, 'Update');
		}
		catch (\RuntimeException $exception)
		{
			// Informational log only
		}

		$this->_applyCredentials();

		/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('Update');

		$model->finaliseUpgrade();

		$url = 'index.php?option=com_joomlaupdate&task=update.cleanup&' . Session::getFormToken() . '=1';
		$this->setRedirect($url);
	}

	/**
	 * Clean up after ourselves
	 *
	 * @return  void
	 *
	 * @since   2.5.4
	 */
	public function cleanup()
	{
		/*
		 * Cleanup with login page. Used for pre-token check versions to be able to update
		 * from =< 3.2.7 to allow updates without problems but with a maximum of security.
		 */
		if (!Session::checkToken('get'))
		{
			$this->setRedirect('index.php?option=com_joomlaupdate&view=update&layout=finaliseconfirm');

			return false;
		}

		$options['format'] = '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}';
		$options['text_file'] = 'joomla_update.php';
		Log::addLogger($options, Log::INFO, array('Update', 'databasequery', 'jerror'));

		try
		{
			Log::add(Text::_('COM_JOOMLAUPDATE_UPDATE_LOG_CLEANUP'), Log::INFO, 'Update');
		}
		catch (\RuntimeException $exception)
		{
			// Informational log only
		}

		$this->_applyCredentials();

		/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('Update');

		$model->cleanUp();

		$url = 'index.php?option=com_joomlaupdate&view=joomlaupdate&layout=complete';
		$this->setRedirect($url);

		try
		{
			Log::add(Text::sprintf('COM_JOOMLAUPDATE_UPDATE_LOG_COMPLETE', \JVERSION), Log::INFO, 'Update');
		}
		catch (\RuntimeException $exception)
		{
			// Informational log only
		}
	}

	/**
	 * Purges updates.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function purge()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Purge updates
		/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('Update');
		$model->purge();

		$url = 'index.php?option=com_joomlaupdate';
		$this->setRedirect($url, $model->_message);
	}

	/**
	 * Uploads an update package to the temporary directory, under a random name
	 *
	 * @return  void
	 *
	 * @since   3.6.0
	 */
	public function upload()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Did a non Super User tried to upload something (a.k.a. pathetic hacking attempt)?
		Factory::getUser()->authorise('core.admin') or jexit(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));

		$this->_applyCredentials();

		/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('Update');

		try
		{
			$model->upload();
		}
		catch (\RuntimeException $e)
		{
			$url = 'index.php?option=com_joomlaupdate';
			$this->setRedirect($url, $e->getMessage(), 'error');
		}

		$token = Session::getFormToken();
		$url = 'index.php?option=com_joomlaupdate&task=update.captive&' . $token . '=1';
		$this->setRedirect($url);
	}

	/**
	 * Checks there is a valid update package and redirects to the captive view for super admin authentication.
	 *
	 * @return  array
	 *
	 * @since   3.6.0
	 */
	public function captive()
	{
		// Check for request forgeries
		Session::checkToken('get') or jexit(Text::_('JINVALID_TOKEN'));

		// Did a non Super User tried to upload something (a.k.a. pathetic hacking attempt)?
		if (!Factory::getUser()->authorise('core.admin'))
		{
			throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Do I really have an update package?
		$tempFile = Factory::getApplication()->getUserState('com_joomlaupdate.temp_file', null);

		\JLoader::import('joomla.filesystem.file');

		if (empty($tempFile) || !File::exists($tempFile))
		{
			throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		$this->input->set('view', 'upload');
		$this->input->set('layout', 'captive');

		$this->display();
	}

	/**
	 * Checks the admin has super administrator privileges and then proceeds with the update.
	 *
	 * @return  array
	 *
	 * @since   3.6.0
	 */
	public function confirm()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Did a non Super User tried to upload something (a.k.a. pathetic hacking attempt)?
		if (!$this->app->getIdentity()->authorise('core.admin'))
		{
			throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Get the model
		/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('Update');

		// Get the captive file before the session resets
		$tempFile = $this->app->getUserState('com_joomlaupdate.temp_file', null);

		// Do I really have an update package?
		if (!$model->captiveFileExists())
		{
			throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Try to log in
		$credentials = array(
			'username'  => $this->input->post->get('username', '', 'username'),
			'password'  => $this->input->post->get('passwd', '', 'raw'),
			'secretkey' => $this->input->post->get('secretkey', '', 'raw'),
		);

		$result = $model->captiveLogin($credentials);

		if (!$result)
		{
			$model->removePackageFiles();

			throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Set the update source in the session
		$this->app->setUserState('com_joomlaupdate.file', basename($tempFile));

		try
		{
			Log::add(Text::sprintf('COM_JOOMLAUPDATE_UPDATE_LOG_FILE', $tempFile), Log::INFO, 'Update');
		}
		catch (\RuntimeException $exception)
		{
			// Informational log only
		}

		// Redirect to the actual update page
		$url = 'index.php?option=com_joomlaupdate&task=update.install&' . Session::getFormToken() . '=1';
		$this->setRedirect($url);
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  static  This object to support chaining.
	 *
	 * @since   2.5.4
	 */
	public function display($cachable = false, $urlparams = array())
	{
		// Get the document object.
		$document = Factory::getDocument();

		// Set the default view name and format from the Request.
		$vName   = $this->input->get('view', 'update');
		$vFormat = $document->getType();
		$lName   = $this->input->get('layout', 'default', 'string');

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			// Get the model for the view.
			/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
			$model = $this->getModel('Update');

			// Push the model into the view (as default).
			$view->setModel($model, true);
			$view->setLayout($lName);

			// Push document object into the view.
			$view->document = $document;
			$view->display();
		}

		return $this;
	}

	/**
	 * Applies FTP credentials to Joomla! itself, when required
	 *
	 * @return  void
	 *
	 * @since   2.5.4
	 */
	protected function _applyCredentials()
	{
		$this->app->getUserStateFromRequest('com_joomlaupdate.method', 'method', 'direct', 'cmd');

		if (!ClientHelper::hasCredentials('ftp'))
		{
			$user = $this->app->getUserStateFromRequest('com_joomlaupdate.ftp_user', 'ftp_user', null, 'raw');
			$pass = $this->app->getUserStateFromRequest('com_joomlaupdate.ftp_pass', 'ftp_pass', null, 'raw');

			if ($user != '' && $pass != '')
			{
				// Add credentials to the session
				if (!ClientHelper::setCredentials('ftp', $user, $pass))
				{
					$this->app->enqueueMessage(Text::_('JLIB_CLIENT_ERROR_HELPER_SETCREDENTIALSFROMREQUEST_FAILED'), 'warning');
				}
			}
		}
	}

	/**
	 * Checks the admin has super administrator privileges and then proceeds with the final & cleanup steps.
	 *
	 * @return  array
	 *
	 * @since   3.6.3
	 */
	public function finaliseconfirm()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Did a non Super User try do this?
		if (!Factory::getUser()->authorise('core.admin'))
		{
			throw new \RuntimeException(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
		}

		// Get the model
		/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
		$model = $this->getModel('Update');

		// Try to log in
		$credentials = array(
			'username'  => $this->input->post->get('username', '', 'username'),
			'password'  => $this->input->post->get('passwd', '', 'raw'),
			'secretkey' => $this->input->post->get('secretkey', '', 'raw'),
		);

		$result = $model->captiveLogin($credentials);

		// The login fails?
		if (!$result)
		{
			$this->setMessage(Text::_('JGLOBAL_AUTH_INVALID_PASS'), 'warning');
			$this->setRedirect('index.php?option=com_joomlaupdate&view=update&layout=finaliseconfirm');

			return false;
		}

		// Redirect back to the actual finalise page
		$this->setRedirect('index.php?option=com_joomlaupdate&task=update.finalise&' . Session::getFormToken() . '=1');
	}

	/**
	 * Fetch Extension update XML proxy. Used to prevent Access-Control-Allow-Origin errors.
	 * Prints a JSON string.
	 * Called from JS.
	 *
	 * @since   4.0.0
	 *
	 * @return void
	 */
	public function fetchExtensionCompatibility()
	{
		$extensionID = $this->input->get('extension-id', '', 'DEFAULT');
		$joomlaTargetVersion = $this->input->get('joomla-target-version', '', 'DEFAULT');

		/** @var JoomlaupdateModelDefault $model */
		$model = $this->getModel('default');
		$updateFileUrl = $model->fetchCompatibility($extensionID, $joomlaTargetVersion);

		$this->app = Factory::getApplication();
		$this->app->mimeType = 'application/json';
		$this->app->charSet = 'utf-8';
		$this->app->setHeader('Content-Type', $this->app->mimeType . '; charset=' . $this->app->charSet);
		$this->app->sendHeaders();

		try
		{
			echo new JsonResponse($updateFileUrl);
		}
		catch (Exception $e)
		{
			echo $e;
		}

		$this->app->close();
	}
}
