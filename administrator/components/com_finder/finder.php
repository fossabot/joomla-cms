<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if (!\JFactory::getUser()->authorise('core.manage', 'com_finder'))
{
	throw new \JAccessExceptionNotallowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

$controller = \JControllerLegacy::getInstance('Finder');
$controller->execute(\JFactory::getApplication()->input->get('task'));
$controller->redirect();
