<?php
# This bot logs in to testwiki.org and edits the sandbox to say "Hello world!"

error_reporting( E_ALL | E_STRICT );
include 'wikibot.classes.php'; /* The wikipedia classes. */

$bot = 'TestBot';
$wiki = 'Testwiki';

$wpapi	= new wikipediaapi ( '', '', '', $bot, $wiki, true );
$wpq	= new wikipediaquery ( '', '', '', $bot, $wiki, true );
$wpi	= new wikipediaindex ( '', '', '', $bot, $wiki, true );
$user = getWikibotSetting( 'user', $bot, $wiki );
$pass = getWikibotSetting( 'pass', $bot, $wiki );
if ( $wpapi->login( $user, $pass ) != 'true' ) {
    echo "Login failure";
    die();
}
sleep( 1 );

var_dump( $wpapi->edit( 'Sandbox', 'Hello world!', 'This is a test', false, false, null, null, false ) );