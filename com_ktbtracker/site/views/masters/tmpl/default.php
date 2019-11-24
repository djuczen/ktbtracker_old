<?php
/**
 * @package		Joomla.Site
 * @subpackage 	com_ktbtracker
 *
 * @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
 * @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
 *
 * $Id$
 */

defined('JPATH_PLATFORM') or die;

JHtml::addIncludePath(JPATH_BASE . '/components/com_ktbtracker/helpers/html');


$user       = JFactory::getUser();

?>
<form action="<?php echo JRoute::_('index.php?opt=com_ktbtracker&view=masters'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="jsn-p">
    <?php if (empty($this->items)) : ?>
    	<div class="alert alert-no-items">
    		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
    	</div>
    <?php else : ?>
    	<table class="table table-bordered table-hover" id="candidatelist">
    		<tbody>
			<?php 
			 foreach ($this->items as $i => $item) :
			     dump($item, "Item $i");
			?>
    			<tr>
    				<td>
    					<div class="jsn-p-top jsn-p-top-a">
    						<div class="jsn-p-avatar">
            					<a href="<?php echo $item->link; ?>">
            						<img src="<?php echo $item->profile->avatar_mini; ?>" alt="<?php echo $this->escape($item->display_name); ?>"
            							class="hasTooltip" title="<?php echo $this->escape($item->profile->firstname); ?>">
            					</a>
            				</div>
            				<div class="jsn-p-title">
            					<h3><?php echo $this->escape($item->display_name); ?></h3>
            					<div data-toggle="tooltip" title="<?php echo ($item->online) ? JText::_('COM_KTBTRACKER_STATUS_ONLINE') : JText::_('COM_KTBTRACKER_STATUS_OFFLINE'); ?>"  class="status label label-<?php echo ($item->online) ? 'success' : 'danger'; ?>"></div>
            				</div>
            				<div class="jsn-p-before-fields">
            					<div class="jsn-p-dates">
            						<div class="jsn-p-date-reg">
            							<b><i class="fa fa-line-chart"></i> <?php echo JText::_('COM_KTBTRACKER_FIELD_LAST_TRACKED_LABEL'); ?>: </b>
            							<?php echo JHtml::_('ktbformat.date', $item->last_tracked); ?>
            						</div>
            						<div class="jsm-p-date-last">
            							<b><i class="fa fa-newspaper-o"></i> <?php echo JText::_('COM_KTBTRACKER_FIELD_LAST_JOURNALED_LABEL'); ?>: </b>
            							<?php echo JHtml::_('ktbformat.date', $item->last_journaled); ?>
            						</div>
            					</div>
            					<div class="jsn-p-dates">
            						<div class="jsn-p-date-reg">
            							<b><i class="fa fa-align-justify"></i> <?php echo JText::_('COM_KTBTRACKER_FIELD_TRACT_LABEL'); ?>: </b>
            							<?php echo $this->escape($item->tract_name); ?>
            						</div>
            					</div>
	           					<div class="jsn-p-dates">
	           						&nbsp;
            					</div>
            				</div>
            				<div class="jsn-p-fields">
            					<fieldset class="jsn-form-fieldset" id="jsn_default" data-index="0" data-name="details">
                					<dl class="dl-horizontal">
                						<dt><?php echo JText::_('COM_KTBTRACKER_FIELD_ABOUTME_LABEL'); ?></dt>
                						<dd>
                							<?php echo array_key_exists('aboutme', $item->profile->profile) ? $this->escape($item->profile->profile['aboutme']) : JText::_('COM_KTBTRACKER_NO_INFORMATION_PROVIDED'); ?>
                						</dd>
                					</dl>
                				</fieldset>
            					<a href="<?php echo JRoute::_('index.php?option=com_ktbtracker&view=tracking&canid=' . (int) $item->id . '&trackingDate=now'); ?>">
            						<?php echo JText::sprintf('COM_KTBTRACKER_LINK_VIEW_TRACKING_LABEL', $this->escape($item->display_name)); ?>
            					</a>
            				</div>
        				</div>
    				</td>
    			</tr>
    			<?php
    			 endforeach;
    			?>
    		</tbody>
    	</table>
    	<?php endif; ?>
    	<?php echo $this->pagination->getListFooter(); ?>
    	
     	<input type="hidden" name="task" value="" />
    	<input type="hidden" name="boxchecked" value="0" />
    	<input type="hidden" name="return" value="<?php echo base64_encode(JUri::getInstance()->toString()); ?>" />
    </div>
</form>
