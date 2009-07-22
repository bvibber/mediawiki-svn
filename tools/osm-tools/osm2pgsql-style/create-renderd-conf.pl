#!/usr/bin/env perl

=head1 NAME

create-renderd-conf.pl - Create the the F</etc/renderd.conf> needed to render l18n-enabled maps

=head1 SYNOPSIS

    perl wikipedia-language-codes.pl > /sql/misc-data/wikipedia-languages.yml
    perl create-renderd-conf.pl --languages /sql/misc-data/wikipedia-languages.yml

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

#
# Config
#

# Our languages
my %languages = %{ LoadFile($languages) };
my @languages = sorted_languages(%languages);

#
# main
#

print <<RENDERD_HEADER;
[renderd]
socketname=/var/run/renderd/renderd.sock
num_threads=4
tile_dir=/sql/mod_tile ; DOES NOT WORK YET
stats_file=/var/run/renderd/renderd.stats

[mapnik]
plugins_dir=/usr/local/lib64/mapnik/input
font_dir=/usr/local/lib64/mapnik/fonts
font_dir_recurse=false
RENDERD_HEADER

for my $language (@languages)
{
    my $code = $language->[0];
    my $name = $language->[1];

    print <<LANGUAGE

;;; Rendering for $code ($name)
[$code]
URI=/tiles/osm-like/$code/
XML=/sql/mapnik-stylesheets/osm-like/osm-$code.xml
HOST=cassini.toolserver.org
LANGUAGE

}

exit 0;

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
