#!/usr/bin/perl
# To do:
# - Preview
# - Diffs
# - Edit conflicts
#
use Config::IniFiles;  # Module for config files in .ini syntax
use LWP::UserAgent;    # Web agent module
use URI::Escape;       # urlencode functions
use Gtk2 '-init';
use Encode qw(encode);

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

	# Do we need to convert UTF-8 into ISO 8859-1?
	if($cfg->val("Settings","Transcode UTF-8") eq "true") {
		$transcode=1;
	}	
	$ct=$response->header('Content-Type');
	$editpage=$response->content;
	$editpage=~m|<input type='hidden' value="(.*?)" name="wpEditToken" />|i;
	$token=$1;
	$editpage=~m|<textarea.*?name="wpTextbox1".*?>(.*?)</textarea>|is;
	$text=$1;
	$editpage=~m|<input type='hidden' value="(.*?)" name="wpEdittime" />|i;
	$time=$1;
	
	# Convert to ISO for easy editing
	if($ct=~m/charset=utf-8/i) {
		$is_utf8=1; 
	}
	if($is_utf8 && $transcode) {
		Encode::from_to($text,'utf8','iso-8859-1');
	}

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

system("$app $tempdir/$filename &");
makegui();

sub makegui {

	$vbox = Gtk2::VBox->new;
	$hbox = Gtk2::HBox->new;
	$label =  Gtk2::Label->new("Summary");
	$entry = Gtk2::Entry->new;
	$hbox->pack_start_defaults($label);
	$hbox->pack_start_defaults($entry);
	
	$hbox2 = Gtk2::HBox->new;
	$savebutton =  Gtk2::Button->new("Save");
	$savecontbutton =  Gtk2::Button->new("Save and continue");
	$cancelbutton = Gtk2::Button->new("Cancel");
	$hbox2->pack_start_defaults($savebutton);
	$hbox2->pack_start_defaults($savecontbutton);
	$hbox2->pack_start_defaults($cancelbutton);
	$vbox->pack_start_defaults($hbox);
	$vbox->pack_start_defaults($hbox2);
	
	# Set up window
	$window = Gtk2::Window->new;
	$window->set_title ('Enter edit summary');
	$window->signal_connect (delete_event => sub {Gtk2->main_quit});
	$savebutton->signal_connect (clicked => \&save);
	$savecontbutton->signal_connect ( clicked => \&savecont);
	$cancelbutton->signal_connect (clicked => \&cancel);
	
	# Add vbox to window
	$window->add($vbox);
	$window->show_all;
	Gtk2->main;

} 

sub savecont {
	
	save("continue");
	
}
sub save {

	my $cont=shift;
	my $summary=$entry->get_text();
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
		$response=$browser->post($edit_url,@ns_headers,Content=>
		[
		wpTextbox1=>$text,
		wpSummary=>$summary,
		wpEdittime=>$time,
		wpEditToken=>$token
		]);
	} elsif($type eq "Diff") {
		die "Diffs not yet supported.\n";
	} else {
	
		die "Undefined or unknown process in input file.";
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
