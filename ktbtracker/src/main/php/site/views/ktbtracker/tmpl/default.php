<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_ktbtracker
 *
 * @copyright   Copyright (C) 2012-${COPYR_YEAR} David Uczen Photography, Inc. All rights reserved.
 * @license     GNU General Public License (GPL) version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;


// !! Bootstrap 4.x / FontAwesome 4.x provided by template !!
HtmlHelper::_('jquery.framework');

// Load ProgressBar.js resources
HTMLHelper::_('stylesheet', 'com_ktbtracker/ktbtracker.css',  array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'com_ktbtracker/ktbtracker.js',  array('version' => 'auto', 'relative' => true));
//HTMLHelper::_('script', 'com_ktbtracker/progressbar.min.js',  array('version' => 'auto', 'relative' => true));
HTMLHelper::_('script', 'com_ktbtracker/jquery.easypiechart.min.js',  array('version' => 'auto', 'relative' => true));

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

$item           = $this->items[0];
$cycle_reqmnts  = array();
if (!empty($this->cycle))
{
    $cycle_reqmnts = explode(',', $this->cycle->cycle_reqmnts);
}
$daysUntil  = KTBTrackerHelper::daysFromCycleStart($this->cycle);
$daysLeft   = KTBTrackerHelper::daysToCycleFinish($this->cycle);
$stats      = KTBTrackerHelper::getCycleStats($this->cycle);
?>
<div class="card-container">
	<div class="card">
		<ul class="list-group list-group-flush">
		<?php foreach (KTBTrackerHelper::getCycleRequirements() as $reqmnt) { ?>
			<?php if (!in_array($reqmnt, $cycle_reqmnts)) { continue; } ?>
			<?php $dailyMin = intval($this->reqmnts->$reqmnt / $daysLeft); ?>
			<?php $dailyPct = intval($stats->$reqmnt * 100); ?>
			<?php $dailyPcnt = min(max($dailyPct, 0), 100);?>
			<?php $dailyTtl = $item->$reqmnt . ' of ' . $dailyMin . ' (' . $this->reqmnts->$reqmnt . ')'; ?>
			<li class="list-group-item">
				<div class="row">
					<!-- Show progress -->
					<div class="col">
						<?php echo HTMLHelper::_('ktbtracker.easypiechart', $reqmnt . '_today', $stats->$reqmnt, $dailyTtl);?>
					</div>
					<!-- Show today's activity -->
					<div class="col text-center">
						<span class="display-4 text-success"><?php echo $item->$reqmnt; ?></span>
					</div>
					<!-- Link to detailed view -->
					<div class="col text-right">
						<i class="fa fa-angle-right fa-2x text-black-50"></i>
					</div>
				</div>
				<div class="row">
					<div class="col text-center">
						<small>
							<span class="text-center"><?php echo Text::_('COM_KTBTRACKER_FIELD_' . $reqmnt . '_SHORT'); ?></span>
						</small>
					</div>
				</div>
			</li>
		<?php } ?>
		</ul>
	</div>
</div>