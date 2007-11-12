<?php

function _fundcore_statistics($year, $month, $day)
{
	if ($year != '')
	{
		$where .= ' AND YEAR(receive_date) = %d ';
	}
	
	if ($month != '')
	{
		$where .= ' AND MONTH(receive_date) = %d ';
	}
	
	if ($day != '')
	{		
		$where .= ' AND DAY(receive_date) = %d ';
	}
	$sql = 'SELECT SUM(total_amount) AS total, AVG(total_amount) AS average, MIN(total_amount) AS minimum, MAX(total_amount) AS maximum, STDDEV(total_amount) AS sd, COUNT(*) AS num FROM civicrm_contribution WHERE total_amount > 0 ' . $where;

	db_set_active('fundcore_civicrm');
	$res =& db_query($sql, $year, $month, $day);
	
	$srows = array();
	while ($row = db_fetch_object($res))
	{
			$srow = array();
			$srow[] = array('data' => number_format($row->num));
			$srow[] = array('data' => number_format($row->minimum, 2));
			$srow[] = array('data' => number_format($row->average, 2));
			$srow[] = array('data' => number_format($row->maximum, 2));
			$srow[] = array('data' => number_format($row->total, 2));
			$srow[] = array('data' => number_format($row->sd, 2));
			$srows[] = $srow;
	}

	db_set_active();

	// Finish table
	$header = array();
	$header[] = array('data' => t('Count'));
	$header[] = array('data' => t('Minimum'));
	$header[] = array('data' => t('Mean'));
	$header[] = array('data' => t('Maximum'));
	$header[] = array('data' => t('Total'));
	$header[] = array('data' => t('Standard Deviation'));
	
	$fields['currencies'] = array(
		'#type' => 'fieldset',
		'#title' => t('Statistical breakdown (%currency)', array('%currency' => 'USD')),
		'#collapsible' => TRUE,
		'#collapsed' => ($day == ''),
	);
	
	$fields['currencies']['table'] = array(
		'#value' => theme('table', $header, $srows, array('style' => 'width: 100%;'))
	);
	
	return drupal_get_form('fundcore_statistical_breakdown', $fields);
}

function _fundcore_currency_summary($year, $month, $day)
{
	if ($year != '')
	{
		$where .= ' AND YEAR(receive_date) = %d ';
	}
	
	if ($month != '')
	{
		$where .= ' AND MONTH(receive_date) = %d ';
	}
	
	if ($day != '')
	{		
		$where .= ' AND DAY(receive_date) = %d ';
	}
	$sql = 'SELECT UPPER(IF(source = "" OR source IS NULL, "USD", source)) AS currency, total_amount FROM civicrm_contribution WHERE total_amount > 0 ' . $where . ' ORDER BY currency';
	
	db_set_active('fundcore_civicrm');
	$res =& db_query($sql, $year, $month, $day);
	db_set_active();
	
	$srows = array();
	$sum_usd = 0;
	$sum = 0;
	$last_currency = NULL;
	while ($row = db_fetch_object($res))
	{
		$currency = substr($row->currency, 0, 3);
			
		if ($last_currency !== NULL && $currency != $last_currency && $sum > 0)
		{		
			$srow = array();
			$srow[] = $last_currency;
			$srow[] = array('style' => 'text-align: right;', 'data' => number_format($sum, 2));
			$srow[] = array('style' => 'text-align: right;', 'data' => number_format($sum_usd, 2));
			$srows[] = $srow;
			$sum = 0;
			$sum_usd = 0;
		}
		
		$amount = str_replace(',', '', trim(substr($row->currency, 4)));
		
		if ($amount != '')
		{
			$sum += $amount;
		} else {
			$sum += $row->total_amount;	
		}
		
		$sum_usd += $row->total_amount;
		
		$last_currency = $currency;
	}
	
	if ($last_currency !== NULL && $sum > 0)
	{
		$srow = array();
		$srow[] = $currency;
		$srow[] = array('style' => 'text-align: right;', 'data' => number_format($sum, 2));
		$srow[] = array('style' => 'text-align: right;', 'data' => number_format($sum_usd, 2));
		$srows[] = $srow;
	}


	// Finish table
	$header = array();
	$header[] = t('Currency');
	$header[] = array('style' => 'text-align: right;', 'data' => t('Amount'));
	$header[] = array('style' => 'text-align: right;', 'data' => t('Equivalent in %currency', array('%currency' => 'USD')));
	
	$fields['currencies'] = array(
		'#type' => 'fieldset',
		'#title' => t('Breakdown by currency'),
		'#collapsible' => TRUE,
		'#collapsed' => ($day == ''),
	);
	
	$fields['currencies']['table'] = array(
		'#value' => theme('table', $header, $srows, array('style' => 'width: 100%;'))
	);
	
	return drupal_get_form('fundcore_currency_summary', $fields);
}

function _fundcore_browse($year, $month, $day)
{
	$breadcrumbs = array();
	$base_link = 'fundcore/browse/';
	
	$title = t('Browsing contributions');
	if ($year != '')
	{
		$breadcrumbs[] = l('browse', $base_link);
		$base_link .= $year . '/';
		$title = t('Browsing year %year', array('%year' => $year));
	}
	
	if ($month != '')
	{
		$breadcrumbs[] = l($year, $base_link);
		$base_link .= $month . '/';
		$title = t('Browsing month %year-%month', array('%year' => $year, '%month' => $month));
	}

	if ($day != '')
	{
		$breadcrumbs[] = l($month, $base_link);
		$base_link .= $day . '/';
		$title = t('Browsing day %year-%month-%day', array('%year' => $year, '%month' => $month, '%day' => $day));
	}
	
	drupal_set_title($title);
	drupal_set_breadcrumb($breadcrumbs);
		
	$sql = NULL;
	if ($year == '')
	{
		$sql = 'SELECT SUM(total_amount) AS total, YEAR(receive_date) AS choice FROM civicrm_contribution WHERE total_amount > 0 GROUP BY YEAR(receive_date) ORDER BY choice';	
	}
	else if ($month == '')
	{
		$sql = 'SELECT SUM(total_amount) AS total, MONTH(receive_date) AS choice FROM civicrm_contribution WHERE total_amount > 0 AND YEAR(receive_date) = %d GROUP BY MONTH(receive_date) ORDER BY choice';
	}
	else if ($day == '')
	{
		$sql = 'SELECT SUM(total_amount) AS total, DAY(receive_date) AS choice FROM civicrm_contribution WHERE total_amount > 0 AND YEAR(receive_date) = %d AND MONTH(receive_date) = %d GROUP BY DAY(receive_date) ORDER BY choice';
	}

	if ($sql !== NULL)
	{
		db_set_active('fundcore_civicrm');
		$res =& db_query($sql, $year, $month, $day);
		db_set_active();
		//$res =& db_query($sql);
		
		$trows = array();
		while ($row = db_fetch_object($res))
		{
			$trow = array();
			$trow[] = l($row->choice, $base_link . $row->choice);
			$trow[] = array('style' => 'text-align: right;', 'data' => number_format($row->total, 2));
			$trows[] = $trow;		
		}

		if (count($trows) > 0)
		{
			// Finish table
			$header = array();
			
			if ($year == '')
				$header[] = t('Year');
			else if ($month == '')
				$header[] = t('Month');	
			else if ($day == '')
				$header[] = t('Day');
		
			$header[] = array('style' => 'text-align: right;', 'data' => t('Total in %currency', array('%currency' => 'USD')));
			
			$output .= theme('table', $header, $trows, array('style' => 'width: 100%;'));
		}
	}
	return $output;	
}