<?php

function _fundcore_filter($params)
{
	$params = _fundcore_list_set_defaults($params);

	$fields['#action'] = url('fundcore/list');
	$fields['#method'] = 'get';
	$fields['#redirect'] = FALSE;

	$fields['filter'] = array(
		'#type' => 'fieldset',
		'#title' => t('Filter contribution results'),
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,
	);

	$fields['filter']['start'] = array(
	 '#type' => 'date',
	 '#title' => t('On or after'),
	 '#default_value' => $params['start'],
	);
	$fields['filter']['end'] = array(
	 '#type' => 'date',
	 '#title' => t('On or before'),
	 '#default_value' => $params['end'],
	);
	
	 $fields['filter']['currency'] = array(
	  '#type' => 'select',
	  '#title' => t('Restrict to a specific currency'),
	  '#default_value' => $params['currency'],
	  '#options' => array(
	    '' => '',
	    'USD' => 'U.S. Dollars',
	    'EUR' => 'Euros',
	    'JPY' => 'Japanese Yen',
	    'CZK' => 'CZK',
	  ),
	);
	
	$fields['filter']['minimum'] = array(
	 '#type' => 'textfield',
	 '#size' => 5,
	 '#title' => t('Minimum value (%currency or equivalent)', array('%currency' => 'USD')),
	 '#default_value' => $params['minimum'],
	);
	
	$fields['filter']['commented'] = array(
	 '#type' => 'checkbox',
	 '#title' => t('Only show contributions with comments'),
	 '#default_value' => $params['commented'],
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

function _fundcore_list_set_defaults($params)
{
	if ($params['minimum'] == '' || $params['minimum'] < 0)
	{
		$params['minimum'] = variable_get('fundcore_default_minimum', '0');	
	}

	if ($params['start'] == '')
	{
		$params['start'] = array('day'=> date('d'),'month'=>date('m'),'year'=>date('Y') - 1);
	}

	if ($params['end'] == '')
	{
		$params['end'] = array('day'=> date('d'),'month'=>date('m'),'year'=>date('Y'));
	}

	return $params;
}

function _fundcore_results($params)
{
	$params = _fundcore_list_set_defaults($params);

	$sql_start = $params['start']['year'] . '-' . $params['start']['month'] . '-' . $params['start']['day'];
	$sql_end = $params['end']['year'] . '-' . $params['end']['month'] . '-' . $params['end']['day'] . ' 23:59';
	
	// Query contributions
	$sql = 'SELECT receive_date, total_amount, c.source, note, display_name, do_not_trade FROM civicrm_contribution c INNER JOIN civicrm_contact p ON contact_id = p.id WHERE receive_date >= "%s" AND receive_date <= "%s" ';
	
	if ($params['commented'] == '1')
	{
		$sql .= ' AND (note <> "" AND note IS NOT NULL) ';
	}

	$sql .= ' AND total_amount >= %f ';

	if ($params['currency'] != '')
	{
		$sql .= ' AND c.source LIKE "%s%%" ';
	}
	$sql .= ' ORDER BY receive_date DESC ';
	
	db_set_active('fundcore_civicrm');
	$res =& pager_query($sql, 25, 0, NULL, $sql_start, $sql_end, $params['minimum'], $params['currency']);
	//$res =& db_queryd($sql, $sql_start, $sql_end, $params['currency'], $params['minimum']);
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
			$name .= '<br />' . htmlentities($row->note, ENT_QUOTES, 'UTF-8');
			//$name .= '<!-- ' . mb_convert_encoding($row->note, 'UTF-16', 'UTF-8') . ' -->';
		}

		$trow = array();
		$trow[] = array('data' => $name, 'style' => 'width: 300px');
		$trow[] = array('data' => check_plain($row->receive_date), 'style' => 'width: 180px;');
		$trow[] = array('data' => check_plain($row->source), 'style' => 'text-align: right;');
		$trow[] = array('data' => check_plain($row->total_amount), 'style' => 'text-align: right;');

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
	$header[] = array('data' => t('Amount'), 'style' => 'text-align: right;');
	$header[] = array('data' => t('Equivalent in %currency', array('%currency' => 'USD')), 'style' => 'text-align: right;');
	//$header[] = t('Comment');
	
	$output .= theme('table', $header, $rows, array('style' => 'width: 100%;'));
	
	// Give a pager
	$output .= theme('pager', NULL, 25, 0);
	
	return $output;
}