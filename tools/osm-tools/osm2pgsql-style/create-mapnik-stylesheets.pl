#!/usr/bin/env perl

=head1 NAME

create-mapnik-stylesheets.pl - Create the the mapnik stylesheets needed to render l18n-enabled maps

=head1 SYNOPSIS

    perl wikipedia-language-codes.pl > /sql/misc-data/wikipedia-languages.yml
    perl create-mapnik-stylesheets.pl \
        --out-dir /sql/mapnik-stylesheets/osm-like \
        --languages /sql/misc-data/wikipedia-languages.yml \
        --osm-template /usr/local/src/osm/applications/rendering/mapnik/osm-template.xml \
        --symbols-dir /usr/local/src/osm/applications/rendering/mapnik/symbols \
        --world-boundaries-dir /sql/world_boundaries/unpacked \
        --psql-host localhost \
        --psql-port 5432 \
        --psql-name gis \
        --psql-user gis \
        --psql-pass gis

=head1 OPTIONS

=over

=item -h, --help

Print a usage message listing all available options

=item --languages

A YAML file to read languages from, e.g. F<wikipedia-languages.yml>

=head1 AUTHOR

E<AElig>var ArnfjE<ouml>rE<eth> Bjarmason <avarab@gmail.com>

=cut

use feature ':5.10';
use strict;
use warnings;

use YAML::Syck qw(LoadFile);

use Getopt::Long;
use Pod::Usage ();
use File::Spec::Functions qw(catfile);

use IO::Handle;

#
# Get command line options
#

Getopt::Long::Parser->new(
	config => [ qw< bundling no_ignore_case no_require_order > ],
)->getoptions(
	'h|help' => \my $help,

	'languages=s' => \my $languages,

    'osm-template=s' => \my $osm_template,
    'symbols-dir=s' => \my $symbols_dir,
    'world-boundaries-dir=s' => \my $world_boundaries_dir,

    'out-dir=s' => \my $out_dir,

    'psql-host=s' => \(my $psql_host = 'localhost'),
    'psql-port=s' => \(my $psql_port = 5432),
    'psql-name=s' => \my $psql_name,
    'psql-user=s' => \my $psql_user,
    'psql-pass=s' => \my $psql_pass,

) or help();

help() if $help;

if (not defined $languages)
{
    warn "--languages must be supplied";
    help();
}

unless (-r $languages)
{
    warn "Can't read the file `$languages'";
    help();
}

if (not defined $osm_template)
{
    warn "--osm-template must be supplied";
    help();
}

unless (-r $osm_template)
{
    warn "Can't read the file `$osm_template'";
    help();
}

if (not defined $psql_user)
{
    warn "--psql-user must be supplied";
    help();
}

#
# Config
#

# Our languages
my %languages = %{ LoadFile($languages) };
my @languages = sorted_languages(%languages);

#
# main
#

my $osm_xml_template = cat($osm_template);
$osm_xml_template = generic_replacement($osm_xml_template);

for my $language (@languages)
{
    my $code = $language->[0];
    my $name = $language->[1];

    my $basename = "osm-$code.xml";
    my $out_file = catfile($out_dir, $basename);

    my $language_template = language_replacement($osm_xml_template, $code);

    say STDERR "Writing osm.xml for language $code to $out_file";
    open my $fh, ">", $out_file or die "Can't open `$out_file' for writing";
    print $fh $language_template;
    $fh->sync;
    close $fh or die "Can't close `$out_file'";
}

#print $osm_xml_template;

exit 0;

sub generic_replacement
{
    my $xml = shift;

    my %replace = (
        SYMBOLS_DIR => $symbols_dir,
        WORLD_BOUNDARIES_DIR => $world_boundaries_dir,
    
        DBHOST => $psql_host,
        DBPORT => $psql_port,
        DBNAME => $psql_name,
        DBUSER => $psql_user,
        DBPASS => $psql_pass,
    );

    while (my ($k, $v) = each %replace)
    {
        $xml =~ s/%$k%/$v/g;
    }

    return $xml;
}

sub language_replacement
{
    my ($xml, $code) = @_;

    # Due to https://trac.mapnik.org/ticket/393
    my $munged_code = $code; $munged_code =~ s/-/_/g;

    # %PREFIX%_point
    # view_planet_osm_point_lang_aa
    $xml =~ s/%PREFIX%_(\S+)/view_planet_osm_${1}_lang_${munged_code}/g;

    return $xml;
}

sub cat
{
    my $file = shift;
    open my $fh, "<", $file or die "Can't open $file for reading: $!";
    my $content = join '', <$fh>;
    close $fh;
    return $content;
}

sub sorted_languages
{
    my %lang = @_;
    my @ret;

    for my $key (sort keys %lang)
    {
        push @ret => [ $key, $lang{$key} ];
    }

    @ret;
}

    
sub help
{
    require Pod::Usage;
    my %arg = @_;

    Pod::Usage::pod2usage(
        -verbose => $arg{ verbose },
        -exitval => $arg{ exitval } || 0,
    );
}
