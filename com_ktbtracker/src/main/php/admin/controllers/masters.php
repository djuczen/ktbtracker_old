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

defined('JPATH_PLATFORM') or die;

use Joomla\Utilities\ArrayHelper;


/**
 * KTBTracker component task controller for master lists (Administration).
 * 
 * @since	1.0.0
*/
class KTBTrackerControllerMasters extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $text_prefix = 'COM_KTBTRACKER_MASTERS';

	/**
	 * Constructor.
	 * 
	 * @param 	array $config An optional associative array of configuration settings.
	 * 
	 * @return 	KTBTrackerControllerCandidates
	 * @see 	JController
	 * @since	1.0.0
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		$this->registerTask('active', 'setStatus');
		$this->registerTask('graduated', 'setStatus');
		$this->registerTask('auditing', 'setStatus');
		$this->registerTask('dnf', 'setStatus');
	}

	public function setStatus()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$user   = JFactory::getUser();
		$ids    = $this->input->get('cid', array(), 'array');
		$values = array(
				'active' => STATUS_CANDIDATE,
				'graduated' => STATUS_GRADUATE,
				'auditing' => STATUS_AUDITING,
				'dnf' => STATUS_DNF
		);
		$task   = $this->getTask();
		$value  = ArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id) {
			if (!$user->authorise('core.edit.state', 'com_ktbtracker.master.' . (int) $id)) {
				// Prune items that you can't change.
				unset($ids[$i]);
				JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'error');
			}
		}

		if (empty($ids))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'warning');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->setStatus($ids, $value)) {
				JFactory::getApplication()->enqueueMessage($model->getError(), 'warning');
			}

			$message = JText::plural($this->text_prefix.'_N_ITEMS_'.$task, count($ids));
		}

		$this->setRedirect(JRoute::_('index.php?option=com_ktbtracker&view=masters', false), $message);
	}
	
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.0.0
	 */
	public function getModel($name = 'Master', $prefix = 'KTBTrackerModel', $config =  array('ignore_request' => true)) 
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}

// Pure PHP - no closing required 
