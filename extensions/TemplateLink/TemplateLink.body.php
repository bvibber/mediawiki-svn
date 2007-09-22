<?php
class TemplateLink extends SpecialPage
{
        function TemplateLink(){
                SpecialPage::SpecialPage("TemplateLink");
                self::loadMessages();
        }
 
        function execute( $par ){
                global $wgOut;

                $this->setHeaders();
 
                # Check if parameter is empty
                $par = trim( $par );
                if( $par == '' ){
                  $wgOut->addWikiText( wfMsg('templatelink_empty') );
                  return;
                }
 
                # Expand template
                $arr = explode( '::' , $par );
                $wikitext = '{{' . implode( '|' , $arr ). '}}';

                # Output
#                $wgOut->addWikiText( $wikitext ); # This works, but is not recommended on mediawiki.org...
                $wgOut->addHTML( $this->sandboxParse( $wikitext ) ); # ...so we'll use this one.
                

                # Setting page tatle based on used template
                $wgOut->setPageTitle( wfMsg( 'templatelink_newtitle' , ucfirst( $arr[0] ) ) );
        }
        
        function sandboxParse($wikiText){
          global $wgTitle, $wgUser;
          $myParser = new Parser();
          $myParserOptions = new ParserOptions();
          $myParserOptions->initialiseFromUser($wgUser);
          $result = $myParser->parse($wikiText, $wgTitle, $myParserOptions);
          return $result->getText();
        }
 
        function loadMessages(){
                static $messagesLoaded = false;
                global $wgMessageCache;
                if( $messagesLoaded )return;
                $messagesLoaded = true;
 
                require( dirname( __FILE__ ). '/TemplateLink.i18n.php' );
                foreach( $allMessages as $lang => $langMessages ){
                        $wgMessageCache->addMessages( $langMessages, $lang );
                }
                return true;
        }
}
