<?php 
//
// Create a candidate record when a subscription is activated.
//
$db = JFactory::getDbo();
$userid = JFactory::getUser()->id;
$timestamp = JFactory::getDate()->toSql();

try {
    // Get the OS Membership Pro field names
    $query = $db->getQuery(true);
    $query
    ->select(array('id', 'name'))
    ->from($db->qn('#__osmembership_fields'));
    $db->setQuery($query);
    $fields = $db->loadAssocList('name', 'id');
    
    // Get field values (if any) for the subscriber
    $query = $db->getQuery(true);
    $query
    ->select(array('field_id', 'field_value'))
    ->from($db->qn('#__osmembership_field_value'))
    ->where($db->qn('subscriber_id') . ' = ' . $row->id);
    $db->setQuery($query);
    $field_values = $db->loadAssocList('field_id', 'field_value');
    
    if (array_key_exists('osm_cycleid', $fields)) {
        if (array_key_exists($fields['osm_cycleid'], $field_values)) {
            $osm_cycleid = $field_values[$fields['osm_cycleid']];
            $query = $db->getQuery(true);
            $query
            ->select($db->qn('title'))
            ->from($db->qn('#__ktbtracker_cycles'))
            ->where($db->qn('id') . ' = ' . $osm_cycleid);
            $db->setQuery($query);
            $osm_cycleid_name = $db->loadResult();
        } else {
            $osm_cycleid = 0;
            $osm_cycleid_name = 'Unknown';
        }
    } else {
        $osm_cycleid = 0;
        $osm_cycleid_name = 'Unknown';
    }
    
    if (array_key_exists('osm_cyclerank', $fields)) {
        if (array_key_exists($fields['osm_cyclerank'], $field_values)) {
            $osm_cyclerank = $field_values[$fields['osm_cyclerank']];
            $query = $db->getQuery(true);
            $query
            ->select($db->qn('title'))
            ->from($db->qn('#__usergroups'))
            ->where($db->qn('id') . ' = ' . $osm_cyclerank);
            $db->setQuery($query);
            $osm_cyclerank_name = $db->loadResult();
        } else {
            $osm_cyclerank = 0;
            $osm_cyclerank_name = 'Unknown';
        }
    } else {
        $osm_cyclerank = 0;
        $osm_cyclerank_name = 'Unknown';
    }
    
    if (array_key_exists('osm_cycleadult', $fields)) {
        if (array_key_exists($fields['osm_cycleadult'], $field_values)) {
            $osm_cycleadult = $field_values[$fields['osm_cycleadult']];
            $query = $db->getQuery(true);
            $query
            ->select($db->qn('title'))
            ->from($db->qn('#__usergroups'))
            ->where($db->qn('id') . ' = ' . $osm_cycleadult);
            $db->setQuery($query);
            $osm_cycleadult_name = $db->loadResult();
        } else {
            $osm_cycleadult = 0;
            $osm_cycleadult_name = 'Unknown';
        }
    } else {
        $osm_cycleadult = 0;
        $osm_cycleadult_name = 'Unknown';
    }
    
    // Check if the candidate record already exists
    $query = $db->getQuery(true);
    $query
    ->select('COUNT(*)')
    ->from($db->qn('#__ktbtracker_candidates'))
    ->where($db->qn('userid') . ' = ' . $row->user_id)
    ->where($db->qn('cycleid') . ' = ' . $osm_cycleid);
    $db->setQuery($query);
    $exists = $db->loadResult();
    
    // Insert the candidate if it doesn't exist
    if (!$exists) {
        $columns = array('userid', 'cycleid', 'tract', 'access', 'status', 'created', 'created_by');
        $values = array($db->q($row->user_id), $osm_cycleid, $osm_cyclerank, $osm_cycleadult, 0, $db->q($userid), $db->q($timestamp));
        $query = $db->getQuery(true);
        $query
        ->insert($db->qn('#__ktbtracker_candidates'))
        ->columns($db->qn($columns))
        ->values(implode(',',$values));
        $db->setQuery($query);
        try {
            $db->execute();
            JFactory::getApplication()->enqueueMessage(
                'Candidate ' . $row->first_name . ' ' . $row->last_name . 
                ' was successfully added to cycle ' . $osm_cycleid_name .
                ' as a ' . $osm_cyclerank_name . ' using the ' . $osm_cycleadult_name . ' curriculum.', 'notice');
        } catch (RuntimeException $e) {
            JFactory::getApplication()->enqueueMessage(
                'Candidate ' . $row->first_name . ' ' . $row->last_name . 
                ' could not be added to cycle ' . $osm_cycleid_name .
                ' due to processing error: ' . $e->getMessage(), 'error');
        }
    } else {
        JFactory::getApplication()->enqueueMessage(
            'Candidate ' . $row->first_name . ' ' . $row->last_name .
            ' already exists on cycle ' . $osm_cycleid_name .
            '. Please verify the correct testing rank and curriculum.', 'notice');
    }
} catch (RuntimeException $e) {
    JFactory::getApplication()->enqueueMessage(
        'Unable to process King Tiger Black Belt data: ' . $e->getMessage() .
        '. Please update the candidate data accordingly.', 'error');
}