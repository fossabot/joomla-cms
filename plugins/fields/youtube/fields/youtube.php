<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Youtube
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

/**
 * Fields Youtube form field
 *
 * @since  __DEPLOY_VERSION__
 */

class JFormFieldYoutube extends JFormFieldText
{

	public $type = 'Youtube';

}
