#!/usr/bin/perl
# To do:
# - Preview
# - Summaries via GTK
# - Diffs
# - Deal with UTF-8/iso8559-1 where possible
#
use Config::IniFiles;  # Module for config files in .ini syntax
use LWP::UserAgent;    # Web agent module
use URI::Escape;       # urlencode functions

# Pfad der Konfigurationsdatei ggf. anpassen!
$cfgfile=$ENV{HOME}."/.ee-helper/ee.ini";

my $cfg = new Config::IniFiles( -file => $cfgfile );
my $args=join(" ",@ARGV);

my $tempdir=$cfg->val("Settings","Temp Path") or 
die "No path for temporary files specified. Please edit $cfgfile and add an entry like this:
[Settings]
Temp Path=/tmp\n";

# Slashes am Ende entfernen
$/="/";  
chomp($tempdir);

#open(DEBUGLOG,">$tempdir/debug.log");

#-------- debug the input file
# open(INPUT,"<$args");
# $/=undef; # slurp mode
# while(<INPUT>) {
# $inputfile=$_;
# }
# print DEBUGLOG $inputfile;
# close(DEBUGLOG);
# exit 0;

if(-e $args) {
	$input = new Config::IniFiles( -file => $args );
} else {
	print "No input file specified.\n";
	print "Syntax: perl ee.pl <Resource description file>\n";
	exit 1;
}
$browser=LWP::UserAgent->new();
$browser->cookie_jar( {} );
@ns_headers = (
   'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20041107 Firefox/1.0',
   'Accept' => 'image/gif, image/x-xbitmap, image/jpeg,
        image/pjpeg, image/png, */*',
   'Accept-Charset' => 'iso-8859-1,*,utf-8',
   'Accept-Language' => 'en-US',
);


$fileurl=$input->val("File","URL");
$type=$input->val("Process","Type");
$script=$input->val("Process","Script");

if($type eq "Edit file") {
	$filename=substr($fileurl,rindex($fileurl,"/")+1);	
} elsif($type eq "Edit text") {
	$fileurl=~m|\?title=(.*?)\&action=|i;
	$pagetitle=$1;
	$filename=uri_unescape($pagetitle);
	$filename=$filename.".wiki";
}

$login_url=$script."?title=Special:Userlogin&action=submitlogin";
$upload_url=$script."?title=Special:Upload";
$edit_url=$script."?title=$pagetitle&action=submit";

@sections=$cfg->Sections();
foreach $section(@sections) {
	if($search=$cfg->val($section,"URL")) {		
		if(index($fileurl,$search)>=0) {
			$username=$cfg->val($section,"Username");
			$password=$cfg->val($section,"Password");
		}
	}

}

# Log into server
$response=$browser->post($login_url,@ns_headers,
Content=>[wpName=>$username,wpPassword=>$password,wpRemember=>"1",wpLoginAttempt=>"Log in"]);
if($response->code!=302 && !$ignore_login_error) {
	die "Could not login with username '$username' and password '$password'.\n"
}

$response=$browser->get($fileurl);
if($type eq "Edit file") {

	open(OUTPUT,">$tempdir/".$filename);
	print OUTPUT $response->content;
	close(OUTPUT);

}elsif($type eq "Edit text") {
	
	$editpage=$response->content;
	$editpage=~m|<input type='hidden' value="(.*?)" name="wpEditToken" />|i;
	$token=$1;
	$editpage=~m|<textarea.*?name="wpTextbox1".*?>(.*?)</textarea>|is;
	$text=$1;
	$editpage=~m|<input type='hidden' value="(.*?)" name="wpEdittime" />|i;
	$time=$1;
	open(OUTPUT,">$tempdir/".$filename);
	print OUTPUT $text;
	close(OUTPUT);
	
}

$ext=$input->val("File","Extension");
# Search for extension-associated application

@extensionlists=$cfg->Parameters("Editors");
foreach $extensionlist(@extensionlists) {
	@exts=split(",",$extensionlist);
	foreach $extensionfromlist(@exts) {
		if ($extensionfromlist eq $ext) { 
			$app=$cfg->val("Editors",$extensionlist);
		}
	}
}

system("$app $tempdir/$filename");
# Some programs terminate immediately .. this is a quick hack to deal 
# with them; optimally, we shouldn't proceed after the GTK summary
# box, though.
#system("kdialog --msgbox 'Text editor terminated.'");

if($type eq "Edit file") {
	$response=$browser->post($upload_url,
	@ns_headers,Content_Type=>'form-data',Content=>
	[
	wpUploadFile=>["$tempdir/".$filename],
	wpUploadDescription=>"Uploaded with External-Editor by Erik Moeller",
	wpUploadAffirm=>"1",
	wpUpload=>"Upload file",
	wpIgnoreWarning=>"1"
	]);
} elsif($type eq "Edit text") {	
 	open(TEXT,"<$tempdir/".$filename);
	$/=undef;
	while(<TEXT>) {
		$text=$_;
	}
	close(TEXT);		
	$response=$browser->post($edit_url,@ns_headers,Content=>
	[
        wpTextbox1=>$text,
	wpSummary=>"Edited with External-Editor by Erik Moeller",
	wpEdittime=>$time,
	wpEditToken=>$token
        ]);
} elsif($type eq "Diff") {
	die "Diffs not yet supported.\n";
} else {

	die "Undefined or unknown process in input file.";
}


#close(DEBUGLOG);
exit 0;