<?php

class fundcore_contribution
{
	public $contact_id;
	public $gateway;
	public $transaction_id;
	public $currency;
	public $amount;
	public $note;
	public $datetime;
	public $fee;

	public function save()
	{
		if (!is_numeric($this->contact_id))
		{
			return false;
		}
		
		$civicrm =& wm_db_connect('civicrm');
		$civicrm_config =& wm_config('civicrm');
		
		$contribution_type_id = $civicrm_config['contribution_type_id_' . $this->gateway];
		$total_amount = wm_convert_to_usd($this->currency, $this->amount);
		$fee_amount = wm_convert_to_usd($this->currency, $this->fee);
		$net_amount = $total_amount - $fee_amount;
		
		if ($total_amount == 0)
			return FALSE;
		
		$source = $this->currency . ' ' . $this->amount;
		
		$sql  = 'INSERT INTO civicrm_contribution (domain_id, contact_id, contribution_type_id, receive_date, total_amount, trxn_id, source, note, fee_amount, net_amount) VALUES (';
		$sql .= $civicrm->quote($civicrm_config['domain_id']) . ',';
		$sql .= $civicrm->quote($this->contact_id) . ',';
		$sql .= $civicrm->quote($contribution_type_id) . ',';
		
		if ($this->datetime == '')
		{
			$sql .= 'NOW(),';
		} else {
			$sql .= $civicrm->quote($this->datetime) . ',';	
		}		
		$sql .= $civicrm->quote($total_amount) . ',';
		$sql .= $civicrm->quote($this->transaction_id) . ',';
		$sql .= $civicrm->quote($source) . ',';
		$sql .= $civicrm->quote($this->note) . ',';
		$sql .= $civicrm->quote($fee_amount) . ',';
		$sql .= $civicrm->quote($net_amount);
		$sql .= ')';
		
		$res =& $civicrm->query($sql);
		if (PEAR::isError($res))
		{
			wm_log('saving payment failed', $this->gateway, $this->transaction_id, array('this' => $this, 'resource' => $res), true);
			return false;
		}
		
		return true;
	}
}
