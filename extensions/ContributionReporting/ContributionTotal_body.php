<?php

class ContributionTotal extends SpecialPage {
  function ContributionTotal() {
    SpecialPage::SpecialPage('ContributionTotal');
    wfLoadExtensionMessages('ContributionTotal');
  }
  
  function execute( $par ) {
    global $wgRequest, $wgOut;
  
    $this->setHeaders();
  
    # Get request data from, e.g.
    $start = $wgRequest->getText('start');
    $action = $wgRequest->getText('action');
    
    $db = contributionReportingConnection();

    $sql = 'SELECT SUM(converted_amount) AS ttl FROM public_reporting';
    
    if ($start) {
      $sql .= ' WHERE received >= ' . $db->addQuotes($start);
    }
  
    $res = $db->query($sql);
    
    $row = $res->fetchRow();
    
    # Output
    $output = $row['ttl'] ? $row['ttl'] : '0';
    
    header('Cache-Control: max-age=300,s-maxage=300');
    if ($action == 'raw') {
      $wgOut->disable();
      echo $output;
    }
    else {    
      $wgOut->setRobotpolicy( 'noindex,nofollow' );
      $wgOut->addHTML( $output );
    }
  }
}
