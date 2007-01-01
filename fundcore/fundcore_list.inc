<?php

function _fundcore_default_start()
{
	return array('day'=> date('d'),'month'=>date('m'),'year'=>date('Y') - 1);
}

function _fundcore_filter($start_param, $end_param, $commented, $currency, $minimum)
{
	$fields['#action'] = url('fundcore/list');
	$fields['#method'] = 'get';
	$fields['#redirect'] = FALSE;
	$fields['filter'] = array(
		'#type' => 'fieldset',
		'#title' => t('Filter contribution results'),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,
	);

	$start = _fundcore_default_start();

	if ($start_param != '')
	{
		$split = explode('-', $start_param);
		$start = array('day'=> $split[2],'month'=>$split[1],'year'=> $split[0]);
	}

	$end = array('day'=> date('d'),'month'=>date('m'),'year'=>date('Y'));

	if ($end_param != '')
	{
		$split = explode('-', $end_param);
		$end = array('day'=> $split[2],'month'=>$split[1],'year'=> $split[0]);
	}

	$fields['filter']['start'] = array(
	 '#type' => 'date',
	 '#title' => t('On or after'),
	 '#default_value' => $start,
	);
	$fields['filter']['end'] = array(
	 '#type' => 'date',
	 '#title' => t('On or before'),
	 '#default_value' => $end,
	);
	
	 $fields['filter']['currency'] = array(
	  '#type' => 'select',
	  '#title' => t('Restrict to a specific currency'),
	  '#default_value' => $currency,
	  '#options' => array(
	    '' => '',
	    'USD' => 'U.S. Dollars',
	    'EUR' => 'Euros',
	    'JPY' => 'Japanese Yen',
	    'CZK' => 'CZK',
	  ),
	);	
	
	
	$fields['filter']['only_with_comments'] = array(
	 '#type' => 'checkbox',
	 '#title' => t('Only show contributions with comments'),
	 '#default_value' => $commented,
	);	

	$fields['filter']['minimum'] = array(
	 '#type' => 'textfield',
	 '#title' => t('Minimum value (%currency or equivalent)', array('%currency' => 'USD')),
	 '#default_value' => $minimum,
	);
	
	$fields['filter']['submit'] = array(
	 '#type' => 'submit',
	 '#value' => t('View results'),
	);
	
	return drupal_get_form('fundcore_filter', $fields);
}

/*
function fundcore_filter_submit($form_id, $edit)
{
	drupal_goto('fundcore/list/' . $edit['start']['year'] . '-' . $edit['start']['month'] . '-' . $edit['start']['day'] . '/' .$edit['end']['year'] . '-' . $edit['end']['month'] . '-' . $edit['end']['day'] . '/' . $edit['only_with_comments'] . '/' . $edit['currency'] . '/' . $edit['minimum'] . '/');
}
*/

function _fundcore_results($start_param, $end_param, $commented, $currency, $minimum)
{
	// Set bounding dates
	$start = _fundcore_default_start();	
	if ($start_param != '')
	{
		$split = explode('-', $start_param);
		$start = array('day'=> $split[2],'month'=>$split[1],'year'=> $split[0]);
	}
	
	$end = array('day'=> date('d'),'month'=>date('m'),'year'=>date('Y'));
	if ($end_param != '')
	{
		$split = explode('-', $end_param);
		$end = array('day'=> $split[2],'month'=>$split[1],'year'=> $split[0]);
	}
	$sql_start = $start['year'] . '-' . $start['month'] . '-' . $start['day'];
	$sql_end = $end['year'] . '-' . $end['month'] . '-' . $end['day'] . ' 23:59';
	
	// Query contributions
	$sql = 'SELECT receive_date, total_amount, c.source, note, display_name, do_not_trade FROM civicrm_contribution c INNER JOIN civicrm_contact p ON contact_id = p.id WHERE total_amount > 0 AND receive_date >= "%s" AND receive_date <= "%s" ';
	
	if ($commented == '1')
	{
		$sql .= ' AND (note <> "" AND note IS NOT NULL) ';
	}

	if ($currency != '')
	{
		$sql .= ' AND (c.source LIKE "s%%") ';
	}

	if ($minimum != '')
	{
		$sql .= ' AND total_amount >= %d';
	}
	
	$sql .= ' ORDER BY receive_date DESC ';
	db_set_active('fundcore_civicrm');
	$res =& pager_query($sql, 25, 0, NULL, $sql_start, $sql_end, $currency, $minimum);
	db_set_active();

	//$res =& db_queryd($sql, $sql_start, $sql_end);

	if ($res === FALSE)
	{
		drupal_set_message('Contribution query error.', 'error');
		return '';
	}


	// Construct output rows
	$rows = array();
	while ($row = db_fetch_object($res))
	{
		if ($row->do_not_trade == 0)
		{
			$name = '<strong>' . htmlentities($row->display_name, ENT_QUOTES, 'UTF-8') . '</strong>';
	 	} else {
			$name = '<strong>' . t('Anonymous') . '</strong>';
	 	}
	 		
		
		if ($row->note != '')
		{
			//$name .= '<br />' . htmlentities($row->note);
			//$name .= '<br />' . htmlentities(mb_convert_encoding($row->test_note, 'windows-1252', 'UTF-8'));
			//$name .= '<br />' . htmlentities(mb_convert_encoding($row->note, 'windows-1252', 'UTF-8'));
			$name .= '<br />' . htmlentities($row->note, ENT_QUOTES, 'UTF-8');
			$name .= '<!-- ' . mb_convert_encoding($row->note, 'UTF-16', 'UTF-8') . ' -->';
		}

		$trow = array();
		$trow[] = array('data' => $name, 'style' => 'width: 300px');
		$trow[] = array('data' => check_plain($row->receive_date), 'style' => 'width: 180px');
		$trow[] = check_plain($row->source);
		$trow[] = check_plain($row->total_amount);

		$rows[] = $trow;
	}

	// Error for no contributions
	if (count($rows) == 0)
	{
		return '<p>' . t('Sorry, there are no matching contributions.') . '</p>';
	}

	// Finish table
	$header = array();
	
	$header[] = t('Name and Comment');
	$header[] = array('data' => t('Time (%timezone)', array('%timezone' => 'UTC')), 'style' => 'width: 60px;');
	$header[] = t('Amount');
	$header[] = t('Equivalent in %currency', array('%currency' => 'USD'));
	//$header[] = t('Comment');
	
	$output .= theme('table', $header, $rows, array('style' => 'width: 100%;'));
	
	// Give a pager
	$output .= theme('pager', NULL, 25, 0);
	
	return $output;
}