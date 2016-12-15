<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Lightbox
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('media');

/**
 * Fields Lightbox form field
 *
 * @since  __DEPLOY_VERSION__
 */

class JFormFieldLightbox extends JFormFieldMedia
{

	public $type = 'Lightbox';

}
