<?php

#(c) Joerg Baach 2007 GPL

class FlaggedRevsPage extends SpecialPage
{
        function FlaggedRevsPage() {
                SpecialPage::SpecialPage("FlaggedRevsPage");
        }

        function execute( $par ) {
                global $wgRequest, $wgOut, $wgUser;

                self::loadMessages();
                $this->setHeaders();

                $dimensions = $wgRequest->getIntArray('dimensions');
                $id =   $wgRequest->getInt('fr_rev_id');

                $db =& wfGetDB(DB_MASTER);
                $user = $wgUser->getId();
                $timestamp =  wfTimestampNow();
    

                foreach ($dimensions as $key=>$val) {
                    $data[] = array ('fr_rev_id' => $id,
                                     'fr_dimension' =>$key,
                                     'fr_flag' => $val,
                                     'fr_user' => $user,
                                     'fr_timestamp' =>  $timestamp,
                                     'fr_comment'=>'testing');
                }
                $db->insert('flaggedrevs',$data);

                
                # Output
                $wgOut->redirect($_SERVER['SCRIPT_NAME']."/$par");
                $wgOut->addHTML("This page was called to modifiy $par" );
        }

        function loadMessages() {
                static $messagesLoaded = false;
                global $wgMessageCache;
                if ( $messagesLoaded ) return;
                $messagesLoaded = true;

                require( dirname( __FILE__ ) . '/FlaggedRevsPage.i18n.php' );
                foreach ( $allMessages as $lang => $langMessages ) {
                        $wgMessageCache->addMessages( $langMessages, $lang );
                }
        }
}
?>
