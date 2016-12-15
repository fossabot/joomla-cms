<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.LigAttachmentshtbox
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (!key_exists('field', $displayData))
{
	return;
}

$field = $displayData['field'];
$attachmentsimage = $field->value;

if (!$attachmentsimage)
{
	return;
}

// Loading the language
JFactory::getLanguage()->load('plg_fields_attachments', JPATH_ADMINISTRATOR);

$doc = JFactory::getDocument();

// Adding the CSS for the attachments
JHtml::_('stylesheet', 'plg_fields_attachments/attachments.css', array('version' => 'auto', 'relative' => true));

$buffer  = '<div class="attachmentsdiv"><a href="#img1"><img src="';
$buffer .= $attachmentsimage;
$buffer .= '" class="thumbnail"></a><a href="#_" class="attachments" id="img1"><img src="';
$buffer .= $attachmentsimage;
$buffer .= '"></a></div>';

echo $buffer;