<?php

require_once ( getenv('MW_INSTALL_PATH') !== false
    ? getenv('MW_INSTALL_PATH')."/maintenance/commandLine.inc"
    : dirname( __FILE__ ) . '/../../maintenance/commandLine.inc' );

$title = Title::newFromText("Test");

$writer = new SMWWriter($title);

$add = new SMWSemanticData(SMWWikiPageValue::makePage("Test", 0));
$remove = new SMWSemanticData(SMWWikiPageValue::makePage("Test", 0));
$property = SMWPropertyValue::makeUserProperty("population");
$value = SMWDataValueFactory::newPropertyObjectValue($property, false);
$remove->addPropertyObjectValue($property, $value);
$value = SMWDataValueFactory::newPropertyObjectValue($property, "33");
$add->addPropertyObjectValue($property, $value);

print "Sending request\n";
$writer->update( $remove, $add, "testing" );

$error = $writer->getError();
if (!empty($error)) print "Errors\n" . $writer->getError() . "\n";
