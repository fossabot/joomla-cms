<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 * @var   array    $inputType       Options available for this field.
 * @var   string   $accept          File types that are accepted.
 * @var   boolean  $charcounter     Does this field support a character counter?
 */

// Initialize some field attributes.
$autocomplete = !$autocomplete ? 'autocomplete="off"' : 'autocomplete="' . $autocomplete . '"';
$autocomplete = $autocomplete === 'autocomplete="on"' ? '' : $autocomplete;

if ($charcounter)
{
	// Load the js file
	Factory::getDocument()->getWebAssetManager()->enableAsset('short-and-sweet');

	// Set the css class to be used as the trigger
	$charcounter = 'charcount';

	// Set the text
	$counterlabel = 'data-counter-label="' . Text::_('COM_CONFIG_METADESC_COUNTER') . '"';
}

$attributes = array(
	$columns ?: '',
	$rows ?: '',
	!empty($class) ? 'class="form-control ' . $class . $charcounter . '"' : 'class="form-control ' . $charcounter . '"',
	!empty($description) ? 'aria-describedby="' . $name . '-desc"' : '',
	strlen($hint) ? 'placeholder="' . htmlspecialchars($hint, ENT_COMPAT, 'UTF-8') . '"' : '',
	$disabled ? 'disabled' : '',
	$readonly ? 'readonly' : '',
	$onchange ? 'onchange="' . $onchange . '"' : '',
	$onclick ? 'onclick="' . $onclick . '"' : '',
	$required ? 'required' : '',
	$autocomplete,
	$autofocus ? 'autofocus' : '',
	$spellcheck ? '' : 'spellcheck="false"',
	$maxlength ? $maxlength: '',
	!empty($counterlabel)
);


?>
<textarea name="<?php
echo $name; ?>" id="<?php
echo $id; ?>" <?php
echo implode(' ', $attributes); ?> ><?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?></textarea>
