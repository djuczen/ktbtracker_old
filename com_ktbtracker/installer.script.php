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

// No direct access to this file
defined('_JEXEC') or die;

class com_ktbtrackerInstallerScript
{
	/**
	 * The name of the extension specified in the manifest.
	 * 
	 * @var string
	 */
	private $extname;

	/**
	 * The type of the extension specified in the manifest.
	 * 
	 * @var string
	 */
	private $exttype;
	
	/**
	 * The minimum Joomla! version required specified in the manifest.
	 * 
	 * @var string
	 */
	private $extversion;

	/**
	 * The path to the extension being installed on the back-end.
	 * 
	 * @var string
	 */
	private $extpath_admin;
	
	/**
	 * The path to the extension being installed on the front-end.
	 * 
	 * @var string
	 */
	private $extpath_site;
	
	
	/**
	 * The version of the extension specified in the manifest.
	 * 
	 * @var string
	 */
	private $version;
	
	public function preflight($type, $parent)
	{
		$this->exttype = $parent->get('manifest')->attributes()->type;
		$this->extmethod = $parent->get('manifest')->attributes()->method;
		$this->extversion = $parent->get('manifest')->attributes()->version;
		$this->extname = $parent->get('manifest')->name;
		$this->version = $parent->get('manifest')->version;
				
		if (method_exists($parent, 'getPath')) {
			$this->extpath_admin = $parent->getPath('extension_administrator');
			$this->extpath_site = $parent->getPath('extension_site');
		} else {
			$this->extpath_admin = $parent->getParent()->getPath('extension_administrator');
			$this->extpath_site = $parent->getParent()->getPath('extension_site');
		}
		
		echo '<img src="' . JURI::root(true) . '/media/' . $this->extname . '/images/installer.png" alt="' . JText::_($this->extname) . '" align="left" />';
		echo '&nbsp;&nbsp;<h1>"' . JText::_($this->extname) . '" ' . ucfirst($this->exttype) . ' Summary</h1>';
	}
	
	/**
	 * Method to install a component.
	 * 
	 * @param object $parent
	 */
	public function install($parent)
	{
		// Navigate to component back-end after install (uncomment if desired)
		if ($this->exttype == 'component') {
			//$parent->getParent()->setRedirectURL('index.php?option=' . $this->extname);
		}
		echo '<p><b>Successfully installed "' . JText::_($this->extname) . '" ' . ucfirst($this->exttype) . ' version ' . $this->version . '.</b></p>';
	}
	
	public function uninstall($parent)
	{
		echo '<p><b>Successfully uninstalled "' . JText::_($this->extname) . '" ' . ucfirst($this->exttype) . ' version ' . $this->version . '.</b></p>';
	}
	
	public function update($parent)
	{
		// Navigate to component back-end after update (uncomment if desired)
		if ($this->exttype == 'component') {
			//$parent->getParent()->setRedirectURL('index.php?option=' . $this->extname);
		}
		echo '<p><b>Successfully updated "' . JText::_($this->extname) . '" ' . ucfirst($this->exttype) . ' version ' . $this->version . '.</b></p>';
	}
	
	public function postflight($type, $parent)
	{
		if ($type == 'install') {
			//$this->updateParameters($parent);
		}
		if ($type == 'uninstall') {
		}
		if ($type == 'update') {
			//$this->updateParameters($parent);
		}
	}
	
	private function updateParameters($parent)
	{
		$config = $this->extpath_admin . '/config.xml';
		
		if (file_exists($config)) {
			echo '<p>Processing ' . $config . '...</p>';
			
			JForm::addFormPath($this->extpath_admin);
			$form = JForm::getInstance('installer', 'config', array(), false, '/config');

			$params = array();
			foreach ($form->getFieldsets() as $name => $fieldset) {
				foreach ($form->getFieldset($name) as $field) {
					if ($field->getAttribute('name') == 'rules') continue;
					echo $field->getAttribute('name') . ' = ' . $field->getAttribute('default') . '</p>';
					$params[$field->getAttribute('name')] = $field->getAttribute('default');
				}
			}
		
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update($db->qn('#__extensions'));
			$query->set($db->qn('params') . ' = ' . $db->q(json_encode($params)));
			$query->where($db->qn('element') . ' = ' . $db->q($this->extname));
			$db->setQuery($query);
			$db->execute();
		
			if ($db->getErrorNum()) {
				JError::raiseError(null, 'Unable to set parameters!');
				return false;
			}

			echo '<p> The following ' . JText::_($this->extname) . ' ' . ucfirst($this->exttype) . ' parameters were set/updated:</p>';
			echo '<ul>';

			foreach ($params as $k => $v) {
				echo '<li>' . JText::_($this->extname . '_FIELD_' . $k . '_LABEL') . '&nbsp;=&nbsp;' . $v . '</li>';
			}
			
			echo '</ul>';
		}
	}
	
	private function installerResults($type, $parent)
	{
	}
}
?><!-- 
// Pure PHP - No Closure Required
-- ------------------------------------------------------------------------
-- @package		Joomla.Administrator
-- @subpackage 	com_ktbtracker
--
-- @copyright	Copyright (C) 2012-2017 David Uczen Photography, Inc. All Rights Reserved.
-- @license		Licensed Materials - Property of David Uczen Photography, Inc.; see LICENSE.txt
--
-- $Id$
-- ------------------------------------------------------------------------

--
-- BACKUP TABLES
--

ALTER TABLE #__ktbtracker_cycles RENAME AS #__ktbtracker_cycles_PRE300;
ALTER TABLE #__ktbtracker_candidates RENAME AS #__ktbtracker_candidates_PRE300;
ALTER TABLE #__ktbtracker_requirements RENAME AS #__ktbtracker_requirements_PRE300;
ALTER TABLE #__ktbtracker_tracking RENAME AS #__ktbtracker_tracking_PRE300;

--
-- CREATE TABLES
--

--
-- Table structure for table `#__ktbtracker_cycles`
--
--	This table contains 1 row per testing cycle; it contains the cycle dates and
--	requirement goals (NULL to use default, 0 if not required)

$drop_cycles = 'DROP TABLE IF EXISTS ' . $db->qn('#__ktbtracker_cycles');
$create_cycles = 'CREATE TABLE IF NOT EXISTS ' . $db->qn('#__ktbtracker_cycles') . ' ( ' .
    $db->qn'id') . 				' INT(11) 		NOT NULL AUTO_INCREMENT, ' .
    $db->qn('title') . 			' VARCHAR(255) 	COLLATE utf8_general_ci NOT NULL, ' .
    $db->qn('alias') . 			' VARCHAR(255) 	COLLATE utf8_general_ci NOT NULL, ' .
    $db->qn('description') . 		' MEDIUMTEXT 	COLLATE utf8_general_ci NOT NULL, ' .
    $db->qn('cycle_start') . 		' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
    $db->qn('cycle_finish') . 	' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
    $db->qn('cycle_prestart') . 	' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
    $db->qn('cycle_cutoff') . 	' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
    $db->qn('cycle_reqmnts') .	' MEDIUMTEXT 	COLLATE utf8_general_ci NOT NULL, ' .
    $db->qn('cycle_goals') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
    $db->qn('created') . 			' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
    $db->qn('created_by') . 		' INT(10) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
    $db->qn('modified') . 		' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
    $db->qn('modified_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
    $db->qn('checked_out') . 		' INT(10) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
    $db->qn('checked_out_time') . ' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
    'PRIMARY KEY (' . $db->qn('id') . ')' .
    ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1';
    
    --
    -- Table structure for table `#__ktbtracker_candidates`
    --
    --	This table contains 1 row per candidate per cycle, recording cycle-based
    -- 	requirements (not daily).
    
    $drop_masters = 'DROP TABLE IF EXISTS ' . $db->qn('#__ktbtracker_candidates');
    $create_candidates = 'CREATE TABLE IF NOT EXISTS ' . $db->qn('#__ktbtracker_candidates') . ' ( ' .
        $db->qn('id') . 				' INT(11) 		NOT NULL AUTO_INCREMENT, ' .
        $db->qn('userid') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('cycleid') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('cont_cycleid') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('cycle_goals') . 		' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('letters') .			' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('essays') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('tree') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('test_written') .		' DECIMAL(10,2)	NOT NULL DEFAULT ' . $db->q('0,00') . ', ' .
        $db->qn('test_physical') .	' DECIMAL(10,2)	NOT NULL DEFAULT ' . $db->q('0,00') . ', ' .
        $db->qn('tract') . 			' SMALLINT(1) 	NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('adult') . 			' TINYINT(1) 	NOT NULL DEFAULT ' . $db->q('1') . ', ' .
        $db->qn('status') . 			' TINYINT(1) 	NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('hidden') .			' TINYINT(1)	NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('goal_start') . 		' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
        $db->qn('goal_finish') .		' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
        $db->qn('created') . 			' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
        $db->qn('created_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('modified') . 		' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
        $db->qn('modified_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('checked_out') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
        $db->qn('checked_out_time') . ' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
        'PRIMARY KEY (' . $db->qn('id') . '), ' .
        'UNIQUE ' . $db->qn('idx_candidate') . ' (' . $db->qn(array('userid`', 'cycleid')) . ')' .
        ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1';
        
        --
        -- Table structure for table `#__ktbtracker_masters`
        --
        --	This table contains 1 row per master candidate per journey, recording journey-based
        -- 	requirements (not daily).
        
        $drop_masters = 'DROP TABLE IF EXISTS ' . $db->qn('#__ktbtracker_masters');
        $create_masters = 'CREATE TABLE IF NOT EXISTS ' . $db->qn('#__ktbtracker_masters') . ' ( ' .
            $db->qn('id') . 				' INT(11) 		NOT NULL AUTO_INCREMENT, ' .
            $db->qn('userid') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('journey_start') .	' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
            $db->qn('jounery_finish') . 	' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
            $db->qn('inst_camps') . 		' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('lead_class') .		' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('assist_class') .		' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('pre_test') .			' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('assist_testing') .	' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('special_events') . 	' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('video_trng') .		' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('lead_seminar') .		' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('tract') . 			' SMALLINT(1) 	NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('status') . 			' TINYINT(1) 	NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('hidden') .			' TINYINT(1)	NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('created') . 			' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
            $db->qn('created_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('modified') . 		' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
            $db->qn('modified_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('checked_out') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
            $db->qn('checked_out_time') . ' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
            'PRIMARY KEY (' . $db->qn('id') . '), ' .
            'UNIQUE ' . $db->qn('idx_master') . ' (' . $db->('userid') . ')' .
            ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1';
            
            --
            -- Table structure for table `#__ktbtracker_requirements`
            --
            --	This table includes 1 row per set of cycle goals. Candidate may be assigned different cycle
            --  goals depending on age group or other circumstances. ID 0 is always the default goal set.
            
            $drop_requirements = 'DROP TABLE IF EXISTS ' . $db->qn('#__ktbtracker_requirements');
            $create_requirements = 'CREATE TABLE IF NOT EXISTS ' . $db->qn('#__ktbtracker_requirements') . ' ( ' .
                $db->qn('id') . 				' INT(11) 		NOT NULL AUTO_INCREMENT, ' .
                $db->qn('title') . 			' VARCHAR(255) 	COLLATE utf8_general_ci NOT NULL, ' .
                $db->qn('alias') . 			' VARCHAR(255) 	COLLATE utf8_general_ci NOT NULL, ' .
                $db->qn('miles') . 			' DECIMAL(10,2) NOT NULL DEFAULT ' . $db->q('105.00') . ', ' .
                $db->qn('pushups') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('10000') . ', ' .
                $db->qn('situps') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('10000') . ', ' .
                $db->qn('burpees') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('1000') . ', ' .
                $db->qn('kicks') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('10500') . ', ' .
                $db->qn('poomsae') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('1500') . ', ' .
                $db->qn('self_defense') . 	' INT(11)		NOT NULL DEFAULT ' . $db->q('1500') . ', ' .
                $db->qn('sparring') . 		' DECIMAL(10,2) NOT NULL DEFAULT ' . $db->q('210.00') . ', ' .
                $db->qn('jumps') . 			' DECIMAL(10,2)	NOT NULL DEFAULT ' . $db->q('210.00') . ', ' .
                $db->qn('pullups') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('350') . ', ' .
                $db->qn('rolls_falls') . 		' INT(11)		NOT NULL DEFAULT ' . $db->q('1000') . ', ' .
                $db->qn('class_saturday') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('class_weekday') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('class_pmaa') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('class_sparring') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('15') . ', ' .
                $db->qn('class_masterq') .	' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('class_dreamteam') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('class_hyperpro') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('meditation') .		' DECIMAL(10,2)	NOT NULL DEFAULT ' . $db->q('500.00') . ', ' .
                $db->qn('raok') .				' INT(11) 		NOT NULL DEFAULT ' . $db->q('200') . ', ' .
                $db->qn('mentor') .			' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('mentee') .			' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('leadership') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('20') . ', ' .
                $db->qn('leadership2') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('10') . ', ' .
                $db->qn('journals') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('50') . ', ' .
                $db->qn('created') . 			' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
                $db->qn('created_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('modified') . 		' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
                $db->qn('modified_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('checked_out') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('checked_out_time') . ' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
                'PRIMARY KEY (' . $db->qn('id') . ')' .
                ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0';
                
                --
                -- Table structure for table `#__ktbtracker_tracking`
                --
                --	This table includes 1 row per day per candidate, recording all requirements
                --	that can be recorded on a daily basis.
                
                $drop_tracking = 'DROP TABLE IF EXISTS ' . $db->qn('#__ktbtracker_tracking');
                $create_tracking = 'CREATE TABLE IF NOT EXISTS $db->qn('#__ktbtracker_tracking') . ' ( ' .
                $db->qn('id') . 				' INT(11) 		NOT NULL AUTO_INCREMENT, ' .
                $db->qn('tracking_date') . 	' DATE 			NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
                $db->qn('userid') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('cycleid') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('miles') . 			' DECIMAL(10,2) NOT NULL DEFAULT ' . $db->q('0.00') . ', ' .
                $db->qn('pushups') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('situps') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('burpees') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('kicks') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('poomsae') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('self_defense') . 	' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('sparring') . 		' DECIMAL(10,2) NOT NULL DEFAULT ' . $db->q('0.00') . ', ' .
                $db->qn('jumps') . 			' DECIMAL(10,2)	NOT NULL DEFAULT ' . $db->q('0.00') . ', ' .
                $db->qn('pullups') . 			' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('rolls_falls') . 		' INT(11)		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('class_saturday') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('class_weekday') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('class_pmaa') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('class_sparring') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('class_masterq') .	' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('class_dreamteam') .	' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('class_hyperpro') . 	' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('meditation') . 		' DECIMAL(10,2)	NOT NULL DEFAULT ' . $db->q('0.00') . ', ' .
                $db->qn('raok') .				' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('mentor.') .			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('mentee') . 			' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('leadership') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('leadership2') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('created') . 			' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
                $db->qn('created_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('modified') . 		' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
                $db->qn('modified_by') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('checked_out') . 		' INT(11) 		NOT NULL DEFAULT ' . $db->q('0') . ', ' .
                $db->qn('checked_out_time') . ' DATETIME 		NOT NULL DEFAULT ' . $db->quote($db->getNullDate()) . ', ' .
                'PRIMARY KEY (' , $db->('id') . '), ' .
                'UNIQUE ' . $db->qn('idx_daily_tracking') . ' (' . $db->qn(array('tracking_date', 'userid')) . ')' .
                ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1';
                
                
                --
                -- MIGRATE TABLES
                --
                
                INSERT INTO $db->qn('#__ktbtracker_cycles`
  (`id`, `title`, `description`, `cycle_start`, `cycle_finish`, `cycle_prestart`, `cycle_cutoff`, `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`)
  SELECT
   `id`, `name`,  `description`, `cycle_start`, `cycle_finish`, `publish_up`,     `publish_down`, `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time') . '
  FROM $db->qn('#__ktbtracker_cycles_PRE300`;
                
                UPDATE $db->qn('#__ktbtracker_cycles`
  SET cycle_reqmnts = 'miles,pushups,situps,burpees,kicks,poomsae,self_defense,sparring,jumps,pullups,class_saturday,class_weekday,class_pmaa,class_masterq,class_dreamteam,meditation,raok,mentor,mentee,leadership,leadership2',
  cycle_goals = 0
  WHERE `id') . ' <= 12;
      
UPDATE $db->qn('#__ktbtracker_cycles`
                SET cycle_reqmnts = 'miles,pushups,situps,burpees,kicks,poomsae,self_defense,sparring,jumps,pullups,rolls_falls,class_saturday,class_weekday,class_pmaa,class_masterq,class_dreamteam,meditation,raok,mentor,mentee,leadership,leadership2',
                cycle_goals = 0
                WHERE `id') . ' > 12;
                
                
INSERT INTO $db->qn('#__ktbtracker_candidates`
(`id`, `userid`, `cycleid`, `cont_cycleid`, `cycle_goals`, `tract`, `adult`,  `status`, `hidden`, `goal_start`,  `goal_finish`,  `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`)
SELECT
`id`, `userid`, `cycleid`, `cont_cycleid`, 0,             `tract`, `access`, `state`,  0,        `publish_up`,  `publish_down`, `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time') . '
  FROM $db->qn('#__ktbtracker_candidates_PRE300`;

UPDATE $db->qn('#__ktbtracker_candiates`
  SET `tract') . ' = 16
  WHERE `tract') . ' = 0;
      
UPDATE $db->qn('#__ktbtracker_candiates`
SET `tract') . ' = (`tract') . ' + 15)
WHERE `tract') . ' BETWEEN 1 AND 15;


INSERT INTO $db->qn('#__ktbtracker_requirements') . ' (`id`) VALUES(0);


INSERT INTO $db->qn('#__ktbtracker_tracking`
(`id`, `tracking_date`, `userid`, `cycleid`,
    `miles`, `pushups`, `situps`, `burpees`, `kicks`, `poomsae`, `self_defense`, `sparring`, `jumps`, `pullups`, `rolls_falls`,
    `class_saturday`, `class_weekday`, `class_pmaa`, `class_sparring`, `class_masterq`, `class_dreamteam`, `class_hyperpro`,
    `meditation`, `raok`, `mentor`, `mentee`, `leadership`, `leadership2`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`)
    SELECT `id`, `tracking_date`, `userid`, `cycleid`,
    `reqmnt0`, `reqmnt1`, `reqmnt2`, `reqmnt3`, `reqmnt4`, `reqmnt5`, `reqmnt6`, `reqmnt7`, `reqmnt8`, `reqmnt10`, 0,
    `reqmnt11`, `reqmnt12`, `reqmnt16`, `reqmnt15`, `reqmnt13`, `reqmnt14`,  0,
    `reqmnt9`, `reqmnt19`, `reqmnt20`, `reqmnt21`, `reqmnt17`, `reqmnt18`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`
    FROM $db->qn('#__ktbtracker_tracking_PRE300') . ' WHERE `cycleid') . ' < 13;
        
INSERT INTO $db->qn('#__ktbtracker_tracking`
(`id`, `tracking_date`, `userid`, `cycleid`,
    `miles`, `pushups`, `situps`, `burpees`, `kicks`, `poomsae`, `self_defense`, `sparring`, `jumps`, `pullups`, `rolls_falls`,
    `class_saturday`, `class_weekday`, `class_pmaa`, `class_sparring`, `class_masterq`, `class_dreamteam`, `class_hyperpro`,
    `meditation`, `raok`, `mentor`, `mentee`, `leadership`, `leadership2`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`)
    SELECT `id`, `tracking_date`, `userid`, `cycleid`,
    `reqmnt0`, `reqmnt1`, `reqmnt2`, `reqmnt3`, `reqmnt4`, `reqmnt5`, `reqmnt6`, `reqmnt7`, `reqmnt8`, `reqmnt10`, `reqmnt22`,
    `reqmnt11`, `reqmnt12`, `reqmnt16`, `reqmnt15`, `reqmnt13`, `reqmnt14`,  0,
    `reqmnt9`, `reqmnt19`, `reqmnt20`, `reqmnt21`, `reqmnt17`, `reqmnt18`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`
    FROM $db->qn('#__ktbtracker_tracking_PRE300') . ' WHERE `cycleid') . ' = 13;
        
INSERT IGNORE INTO $db->qn('#__ktbtracker_tracking`
(`id`, `tracking_date`, `userid`, `cycleid`,
    `miles`, `pushups`, `situps`, `burpees`, `kicks`, `poomsae`, `self_defense`, `sparring`, `jumps`, `pullups`, `rolls_falls`,
    `class_saturday`, `class_weekday`, `class_pmaa`, `class_sparring`, `class_masterq`, `class_dreamteam`, `class_hyperpro`,
    `meditation`, `raok`, `mentor`, `mentee`, `leadership`, `leadership2`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`)
    SELECT `id`, `tracking_date`, `userid`, `cycleid`,
    `reqmnt0`, `reqmnt1`, `reqmnt2`, `reqmnt3`, `reqmnt4`, `reqmnt5`, `reqmnt6`, `reqmnt7`, `reqmnt8`, `reqmnt10`, `reqmnt30`,
    `reqmnt11`, `reqmnt12`, `reqmnt16`, `reqmnt15`, `reqmnt13`, `reqmnt14`,  0,
    `reqmnt9`, `reqmnt19`, `reqmnt20`, `reqmnt21`, `reqmnt17`, `reqmnt18`,
    `created`, `created_by`, `modified`, `modified_by`, `checked_out`, `checked_out_time`
    FROM $db->qn('#__ktbtracker_tracking_PRE300') . ' WHERE `cycleid') . ' > 13;
    -->
  