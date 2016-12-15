<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.Youtube
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

// extract the Youtube video id 
preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $value, $match);
$youtube_id = $match[1];

// Loading the language
JFactory::getLanguage()->load('plg_fields_youtube', JPATH_ADMINISTRATOR);

$doc = JFactory::getDocument();

// Adding the CSS to make the Youtube video responsive
JHtml::_('stylesheet', 'plg_fields_youtube/youtube_embed.css', array('version' => 'auto', 'relative' => true));

// Youtube Video container
$buffer  = '<div class="embed-container"><iframe src="//www.youtube.com/embed/';
$buffer .= $youtube_id;
$buffer .= '" frameborder="0" allowfullscreen></iframe></div';

echo $buffer;
