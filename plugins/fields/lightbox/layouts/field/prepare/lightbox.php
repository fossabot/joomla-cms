<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Lightbox
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
$value = $field->value;

if (!$value)
{
	return;
}

// Loading the language
JFactory::getLanguage()->load('plg_fields_lightbox', JPATH_ADMINISTRATOR);

$doc = JFactory::getDocument();

// Adding the CSS for the lightbox
JHtml::_('stylesheet', 'plg_fields_lightbox/lightbox.css', array('version' => 'auto', 'relative' => true));

// Youtube Video container
//$buffer  = '<div class="embed-container"><iframe src="//www.youtube.com/embed/';
//$buffer .= $youtube_id;
//$buffer .= '" frameborder="0" allowfullscreen></iframe></div';
//
//echo $buffer;

$buffer  = '<div class="lightboxdiv"><a href="#img1"><img src="';
$buffer .= $lightboximage;
$buffer .= '" class="thumbnail"></a><a href="#_" class="lightbox" id="img1"><img src="';
$buffer .= $lightboximage;
$buffer .= '"></a></div>';

echo $buffer;