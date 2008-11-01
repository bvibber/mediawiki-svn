<?php
class ContributionHistory extends SpecialPage {
  function ContributionHistory() {
    SpecialPage::SpecialPage('ContributionHistory');
    wfLoadExtensionMessages('ContributionHistory');
  }
  
  function execute( $language = NULL ) {
    global $wgRequest, $wgOut;
    
    if (!$language) {
      $language = 'en';
    }
    
    // Get request data
    $dir = $wgRequest->getText('dir', '');
    $offset = $wgRequest->getText('offset');
    $limit = $wgRequest->getText('limit', 50);
  
    $this->setHeaders();
  
    $db = contributionReportingConnection();

    $sql = 'SELECT * FROM public_reporting ORDER BY received DESC LIMIT ' . intval($limit);
    
    $res = $db->query($sql);
    
    $output = '<style type="text/css">';
    $output .= 'td {vertical-align: top; padding: 5px;}';
    $output .= 'td.left {padding-right: 10px;}';
    $output .= 'td.right {padding-left: 10px; text-align: right;}';
    $output .= 'td.alt {background-color: #DDDDDD;}';
    $output .= '</style>';
    
    $output .= '<table style="width: 100%">';
    $output .= '<tr><th style="width: 200px;">Name</th><th>Date</th><th style="text-align: right;">Amount</th></tr>';
    
    $alt = TRUE;
    while ($row = $res->fetchRow()) {
      $name = htmlspecialchars($row['name']);
      if (!$name) {
        $name = 'Anonymous';
      }
      
      $name = '<strong>' . $name . '</strong>';
      
      if ($row['note']) {
        $name .= '<br />' . htmlspecialchars($row['note']);
      }
      
      $amount = htmlspecialchars($row['original_currency'] . ' ' . $row['original_amount']);
      if (!$row['original_currency'] || !$row['original_amount']) {
        $amount = 'USD ' . htmlspecialchars($row['converted_amount']);
      }
      
      $class = '';
      if ($alt) {
        $class = ' alt';
      }
      
      $output .= '<tr><td class="left' . $class . '">' . $name . '</td><td class="left' . $class . '" style="width: 50px;">' . date('Y-m-j', $row['received']) . '</td><td class="right' . $class . '" style="width: 75px;">' . $amount . '</td></tr>';
      
      $alt = !$alt;
    }
    
    $output .= '</table>';
    
    header('Cache-Control: max-age=300,s-maxage=300');
    $wgOut->addWikiText('{{Template:Donate-header/' . $language . '}}');
    $wgOut->addWikiText('<skin>Tomas</skin>');
    $wgOut->addHTML('<h1>Real-time donor comments from around the world</h1>');    
    $wgOut->addWikiText('<strong>{{Template:Contribution history introduction/' . $language . '}}</strong>');
    $wgOut->addHTML( $output );
    $wgOut->addWikiText('{{Template:Donate-footer/' . $language . '}}');
  }
}
