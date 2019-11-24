<?php
/**
 * @package		Joomla.Administrator
 * @subpackage 	com_ktbtracker
 * 
 * @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 * 
 * $Id$
 */

// No direct access to this file
defined('_JEXEC') or die;

// Warning - Joomla! 3.x uses Bootstrap 2.3.2
JHtml::_('bootstrap.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$user			= JFactory::getUser();
$userId			= $user->id;

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "cycle.cancel" || document.formvalidator.isValid(document.getElementById("cycle-form")))
		{
			Joomla.submitform(task, document.getElementById("cycle-form"));
		}
	};
');
?>
<form action="<?php echo JRoute::_('index.php?option=com_ktbtracker&layout=edit&id=' . (int) $this->item->id); ?>" method="post" id="adminForm" name="cycle-form" class="form-validate">
	
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_KTBTRACKER_FIELDSET_DETAILS_LABEL', true)); ?>		
		<div class="row-fluid">
			<div class="span8">
				<fieldset class="adminForm">
					<?php foreach ($this->form->getFieldset('details') as $field) { echo $field->renderField(); } ?>
				</fieldset>
			</div>
			<div class="span4">
				<fieldset class="adminForm">
					<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
				
					<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php foreach ($this->form->getFieldsets() as $name => $fieldSet) : ?>
		<?php if ($name == 'details') { continue; } ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', $name, JText::_($fieldSet->label, true)); ?>		
		<div class="row-fluid">
			<div class="span12">
				<fieldset class="adminForm">
					<p><?php echo JText::_($fieldSet->description); ?></p>
					<?php foreach ($this->form->getFieldset($name) as $field) { echo $field->renderField(); } ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php endforeach; ?>
		
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="cycle.edit"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
