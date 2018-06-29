<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_redirect
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Redirect\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

/**
 * Redirect link list controller class.
 *
 * @since  1.6
 */
class LinksController extends AdminController
{
	/**
	 * Method to update a record.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function activate()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids     = $this->input->get('cid', array(), 'array');
		$newUrl  = $this->input->getString('new_url');
		$comment = $this->input->getString('comment');

		if (empty($ids))
		{
			$this->app->enqueueMessage(Text::_('COM_REDIRECT_NO_ITEM_SELECTED'), 'warning');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			$ids = ArrayHelper::toInteger($ids);

			// Remove the items.
			if (!$model->activate($ids, $newUrl, $comment))
			{
				$this->app->enqueueMessage($model->getError(), 'warning');
			}
			else
			{
				$this->setMessage(Text::plural('COM_REDIRECT_N_LINKS_UPDATED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_redirect&view=links');
	}

	/**
	 * Method to duplicate URLs in records.
	 *
	 * @return  void
	 *
	 * @since   3.6.0
	 */
	public function duplicateUrls()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids     = $this->input->get('cid', array(), 'array');
		$newUrl  = $this->input->getString('new_url');
		$comment = $this->input->getString('comment');

		if (empty($ids))
		{
			$this->app->enqueueMessage(Text::_('COM_REDIRECT_NO_ITEM_SELECTED'), 'warning');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			$ids = ArrayHelper::toInteger($ids);

			// Remove the items.
			if (!$model->duplicateUrls($ids, $newUrl, $comment))
			{
				$this->app->enqueueMessage($model->getError(), 'warning');
			}
			else
			{
				$this->setMessage(Text::plural('COM_REDIRECT_N_LINKS_UPDATED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_redirect&view=links');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix of the model.
	 * @param   array   $config  An array of settings.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel The model instance
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Link', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Executes the batch process to add URLs to the database
	 *
	 * @return  void
	 */
	public function batch()
	{
		$batch_urls_request = $this->input->post->get('batch_urls', array(), 'array');
		$batch_urls_lines   = array_map('trim', explode("\n", $batch_urls_request[0]));

		$batch_urls = array();

		foreach ($batch_urls_lines as $batch_urls_line)
		{
			if (!empty($batch_urls_line))
			{
				$params = JComponentHelper::getParams('com_redirect');
				$batch_urls[] = array_map('trim', explode($params->get('separator', '|'), $batch_urls_line));
			}
		}

		// Set default message on error - overwrite if successful
		$this->setMessage(Text::_('COM_REDIRECT_NO_ITEM_ADDED'), 'error');

		if (!empty($batch_urls))
		{
			$model = $this->getModel('Links');

			// Execute the batch process
			if ($model->batchProcess($batch_urls))
			{
				$this->setMessage(Text::plural('COM_REDIRECT_N_LINKS_ADDED', count($batch_urls)));
			}
		}

		$this->setRedirect('index.php?option=com_redirect&view=links');
	}

	/**
	 * Clean out the unpublished links.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public function purge()
	{
		$model = $this->getModel('Links');

		if ($model->purge())
		{
			$message = Text::_('COM_REDIRECT_CLEAR_SUCCESS');
		}
		else
		{
			$message = Text::_('COM_REDIRECT_CLEAR_FAIL');
		}

		$this->setRedirect('index.php?option=com_redirect&view=links', $message);
	}
}
