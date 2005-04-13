#!/usr/bin/perl
#
# applicaton/x-external-editor 
# reference implementation of the helper application
#
# written by Erik Möller - public domain
#
# User documentation:      http://meta.wikimedia.org/wiki/Help:External_editors
# Technical documentation: http://meta.wikimedia.org/wiki/Help:External_editors/Tech
#
# To do: Edit conflicts
#
require("language.pl");

use Config::IniFiles;  # Module for config files in .ini syntax
use LWP::UserAgent;    # Web agent module for retrieving and posting HTTP data
use URI::Escape;       # Urlencode functions
use Gtk2 '-init';      # Graphical user interface, requires GTK2 libraries
use Encode qw(encode); # UTF-8/iso8859-1 encoding

# By default, config will be searched for in your Unix home directory 
# (e.g. ~/.ee-helper/ee.ini). Change path of the configuration file if needed!
$cfgfile=$ENV{HOME}."/.ee-helper/ee.ini";

$DEBUG=0;
$LANGUAGE="de";

# Read config
my $cfg = new Config::IniFiles( -file => $cfgfile );

# Treat spaces as part of input filename
my $args=join(" ",@ARGV);

# Where do we store our files?
my $tempdir=$cfg->val("Settings","Temp Path") or die _("notemppath",$cfgfile);

# Remove slash at the end of the directory name, if existing
$/="/";  
chomp($tempdir);

if($DEBUG) {
	# Make a copy of the control (input) file in the log
	open(DEBUGLOG,">$tempdir/debug.log");
	open(INPUT,"<$args");
	$/=undef; # slurp mode
	while(<INPUT>) {
	$inputfile=$_;
	}
	print DEBUGLOG $inputfile;
	close(INPUT);
}

# Read the control file
if(-e $args) {
	$input = new Config::IniFiles( -file => $args );
} else {
	die _("nocontrolfile");
}

# Initialize the browser as Firefox 1.0 with new cookie jar
$browser=LWP::UserAgent->new();
$browser->cookie_jar( {} );
@ns_headers = (
   'User-Agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7) Gecko/20041107 Firefox/1.0',
   'Accept' => 'image/gif, image/x-xbitmap, image/jpeg,
        image/pjpeg, image/png, */*',
   'Accept-Charset' => 'iso-8859-1,*,utf-8',
   'Accept-Language' => 'en-US',
);

# Obtain parameters from control file
$fileurl=$input->val("File","URL");
$type=$input->val("Process","Type");
$script=$input->val("Process","Script");
$server=$input->val("Process","Server");
$path=$input->val("Process","Path");
$login_url=$script."?title=Special:Userlogin&action=submitlogin";
$ext=$input->val("File","Extension");

# Edit file: change an image, sound etc. in an external app
# Edit text: change a regular text file
# In both cases, we need to construct the relevant URLs
# to fetch and post the data.
if($type eq "Edit file") {
	$filename=substr($fileurl,rindex($fileurl,"/")+1);
	# Image: is canonical namespace name, should always work
	$view_url=$script."?title=Image:$filename"; 
	$upload_url=$script."?title=Special:Upload";
} elsif($type eq "Edit text") {
	$fileurl=~m|\?title=(.*?)\&action=|i;
	$pagetitle=$1;
	$filename=uri_unescape($pagetitle);
	$filename=$filename.".wiki";
	$edit_url=$script."?title=$pagetitle&action=submit";
	$view_url=$script."?title=$pagetitle";	
} elsif($type eq "Diff text") {
	$secondurl=$input->val("File 2","URL");
	if(!$secondurl) {
		die _("twofordiff");
	}
	$diffcommand=$cfg->val("Settings","Diff");
	if(!$diffcommand) {
		die _("nodifftool");	
	}
} else {
		# Nothing we know!
		die _("unknownprocess");	
}


# Obtain settings from config file
$previewclient=$cfg->val("Settings","Browser");	
$browseaftersave=$cfg->val("Settings","Browse after save");	

# The config file can contain definitions for any number
# of site. Each one of them should have an "URL match", which is
# a simple string expression that needs to be part of the
# URL in the control file in order for it to be recognized
# as that site. Using this methodology, we can define usernames
# and passwords for sites relatively easily.
#
# Here we try to match the URL in the control file against the 
# URL matches in all sections to determine the username and
# password.
#
@sections=$cfg->Sections();
foreach $section(@sections) {
	if($search=$cfg->val($section,"URL match")) {		
		if(index($fileurl,$search)>=0) {
			$username=$cfg->val($section,"Username");
			$password=$cfg->val($section,"Password");
		}
	}

}

# Log into server
$response=$browser->post($login_url,@ns_headers,
Content=>[wpName=>$username,wpPassword=>$password,wpRemember=>"1",wpLoginAttempt=>"Log in"]);

# We expect a redirect after successful login
if($response->code!=302 && !$ignore_login_error) {
	die _("loginfailed",$login_url,$username,$password);
}

$response=$browser->get($fileurl);
if($type eq "Edit file") {

	open(OUTPUT,">$tempdir/".$filename);
	print OUTPUT $response->content;
	close(OUTPUT);

}elsif($type eq "Edit text") {

	# Do we need to convert UTF-8 into ISO 8859-1?
	if($cfg->val("Settings","Transcode UTF-8") eq "true") {
		$transcode=1;
	}
	
	# MediaWiki 1.4+ uses edit tokens, we need to get one 
	# before we can submit edits. So instead of action=raw, we use 
	# action=edit, and get the token as well as the text of the page
	# we want to edit in one go.
	$ct=$response->header('Content-Type');
	$editpage=$response->content;
	$editpage=~m|<input type='hidden' value="(.*?)" name="wpEditToken" />|i;
	$token=$1;
	$editpage=~m|<textarea.*?name="wpTextbox1".*?>(.*?)</textarea>|is;
	$text=$1;
	$editpage=~m|<input type='hidden' value="(.*?)" name="wpEdittime" />|i;
	$time=$1;
	
	# Do we need to convert ..?
	if($ct=~m/charset=utf-8/i) {
		$is_utf8=1; 
	}	
	# ..if so, do it.
	if($is_utf8 && $transcode) {
		Encode::from_to($text,'utf8','iso-8859-1');
	}
	
	# Flush the raw text of the page to the disk
	open(OUTPUT,">$tempdir/".$filename);
	select OUTPUT; $|=1; select STDOUT;
	print OUTPUT $text;
	close(OUTPUT);
	
}

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

# In most cases, we'll want to run the GUI for managing saves & previews,
# and run the external editor application.
if($type ne "Diff text") {

 	system("$app $tempdir/$filename &");
	makegui();

} else {
	# For external diffs, we need to create two temporary files.
	$response1=$browser->get($fileurl);
	$response2=$browser->get($secondurl);
	open(DIFF1, ">$tempdir/diff-1.txt");
	select DIFF1; $|=1; select STDOUT;
	open(DIFF2, ">$tempdir/diff-2.txt");
	select DIFF2; $|=1; select STDOUT;
	print DIFF1 $response1->content;
	print DIFF2 $response2->content;
	close(DIFF1);
	close(DIFF2);
	system("$diffcommand $tempdir/diff-1.txt $tempdir/diff-2.txt");
}
	
# Create the GTK2 graphical user interface
# It should look like this:
#  _______________________________________________
# | Summary: ____________________________________ |
# |                                               |
# | [Save] [Save & Cont.] [Preview] [Cancel]      |
# |_______________________________________________|
#
# Save: Send data to the server and quit ee.pl
# Save & cont.: Send data to the server, keep GUI open for future saves
# Preview: Create local preview file and view it in the browser (for text)
# Cancel: Quit ee.pl
#
sub makegui {

	$vbox = Gtk2::VBox->new;
	$hbox = Gtk2::HBox->new;
	$label =  Gtk2::Label->new(_("summary"));
	$entry = Gtk2::Entry->new;
	$hbox->pack_start_defaults($label);
	$hbox->pack_start_defaults($entry);
	
	$hbox2 = Gtk2::HBox->new;
	$savebutton =  Gtk2::Button->new(_("save"));
	$savecontbutton =  Gtk2::Button->new(_("savecont"));
	$previewbutton =  Gtk2::Button->new(_("preview"));
	$cancelbutton = Gtk2::Button->new(_("cancel"));
	$hbox2->pack_start_defaults($savebutton);
	$hbox2->pack_start_defaults($savecontbutton);
	$hbox2->pack_start_defaults($previewbutton);
	$hbox2->pack_start_defaults($cancelbutton);
	$vbox->pack_start_defaults($hbox);
	$vbox->pack_start_defaults($hbox2);
	
	# Set up window
	$window = Gtk2::Window->new;
	$window->set_title (_("entersummary"));
	$window->signal_connect (delete_event => sub {Gtk2->main_quit});
	$savebutton->signal_connect (clicked => \&save);
	$savecontbutton->signal_connect ( clicked => \&savecont);
	$previewbutton->signal_connect ( clicked => \&preview);	
	$cancelbutton->signal_connect (clicked => \&cancel);
	
	# Add vbox to window
	$window->add($vbox);
	$window->show_all;
	Gtk2->main;

} 

# Just let save function know that it shouldn't quit
sub savecont {
	
	save("continue");
	
}

# Just let save function know that it shouldn't save
sub preview {
	$preview=1;
	save("continue");
}

sub save {

	my $cont=shift;
	my $summary=$entry->get_text();	
	# Spam the summary if room is available :-)
	if(length($summary)<190) {
		my $tosummary="using [[Help:External editors|an external editor]]";
		if(length($summary)>0) {
			$tosummary=" [".$tosummary."]";
		}
		$summary.=$tosummary;
	}
	if($is_utf8) {
		$summary=Encode::encode('utf8',$summary);	
	}
	# Upload file back to the server and load URL in browser
	if($type eq "Edit file") {		
 		$response=$browser->post($upload_url,
 		@ns_headers,Content_Type=>'form-data',Content=>
 		[
 		wpUploadFile=>["$tempdir/".$filename],
 		wpUploadDescription=>$summary,
 		wpUploadAffirm=>"1",
 		wpUpload=>"Upload file",
 		wpIgnoreWarning=>"1"
 		]);
		if($browseaftersave eq "true" && $previewclient && !$preview) {
			$previewclient=~s/\$url/$view_url/i;
			print "View URL: $view_url\n";
			system(qq|$previewclient|);
			$previewclient=$cfg->val("Settings","Browser");	
		} 
	# Save text back to the server & load in browser
	} elsif($type eq "Edit text") {	
		open(TEXT,"<$tempdir/".$filename);
		$/=undef;
		while(<TEXT>) {
			$text=$_;
		}
		close(TEXT);
		if($is_utf8 && $transcode) {
			Encode::from_to($text,'iso-8859-1','utf8');		
		}
		if($preview) {
			$response=$browser->post($edit_url,@ns_headers,Content=>
			[
			wpTextbox1=>$text,
			wpSummary=>$summary,
			wpEdittime=>$time,
			wpEditToken=>$token,
			wpPreview=>"true",
			]);		
			open(PREVIEW,">$tempdir/preview.html");
			$preview=$response->content;
			# Replace relative URLs with absolute ones
			$preview=~s|$path|$server$path|gi;
			print PREVIEW $preview;
			close(PREVIEW);
			if($previewclient) {
				$previewurl="file://$tempdir/preview.html";
				$previewclient=~s/\$url/$previewurl/i;
				system(qq|$previewclient|);
				$previewclient=$cfg->val("Settings","Browser");	
			}
		} else {		
			$response=$browser->post($edit_url,@ns_headers,Content=>
			[
			wpTextbox1=>$text,
			wpSummary=>$summary,
			wpEdittime=>$time,
			wpEditToken=>$token,
			]);		
		}
		if($browseaftersave eq "true" && $previewclient && !$preview) {
			$previewclient=~s/\$url/$view_url/i;
			system(qq|$previewclient|);	
			$previewclient=$cfg->val("Settings","Browser");	
		}
		$preview=0;
	}
	if($cont ne "continue") {
		Gtk2->main_quit;
		exit 0;
	}
}
sub cancel {

	print "Quitting!\n";
	Gtk2->main_quit;

}