#!/usr/bin/env perl

=head1 NAME

create-wikimedia-style - Munge L<osm2pgsql(1)>'s F<default.style> and create a custom F<wikimedia.style>

=head1 SYNOPSIS

    # Read a list of language codes created with
    # wikipedia-language-codes and emit a wikimedia.style
    wikipedia-language-codes.pl > wikipedia-languages.yml
    create-wikimedia-style.pl --languages wikipedia-languages.yml --style /usr/local/src/osm/applications/utils/export/osm2pgsql/default.style > wikimedia.style

=head1 OPTIONS

=over

=item -h, --help

Print a usage message listing all available options

=item --languages

A YAML file to read languages from, e.g. F<wikipedia-languages.yml>

=item --style

The style file to read for munging,
e.g. F</usr/local/src/osm/applications/utils/export/osm2pgsql/default.style>

=head1 AUTHOR

E<AElig>var ArnfjE<ouml>rE<eth> Bjarmason <avarab@gmail.com>

=cut

use strict;
use warnings;

use YAML::Syck qw(LoadFile);

use Getopt::Long;
use Pod::Usage ();

#
# Get command line options
#

Getopt::Long::Parser->new(
	config => [ qw< bundling no_ignore_case no_require_order > ],
)->getoptions(
	'h|help' => \my $help,
	'languages=s' => \my $languages,
    'style=s' => \my $style,
) or help();

help() if $help;

unless (-r $languages)
{
    warn "Can't read the file `$languages'";
    help();
}

unless (-r $style)
{
    warn "Can't read the file `$style'";
    help();
}

#
# main
#

my %languages = %{ LoadFile($languages) };
my @languages = sorted_languages(%languages);

my $default_style = cat($style);
my $wikimedia_style = inject_languages($default_style, @languages);

print $wikimedia_style;

exit 0;

sub inject_languages
{
    my ($style, @lang) = @_;
    my @style = split /^/, $style;
    my @munged;

    for my $line (@style)
    {
        chomp $line;

        unless ($line =~ /^node,way   name/) {
            push @munged => $line;
        } else {
            my $cn = scalar(@lang);
            push @munged =>
            "",
            "##################################################################################",
            "# This is a list of $cn languages for which we want to gather the name:lang tags #",
            "##################################################################################",
            "";
            push @munged => "# name= is the generic fallback";
            push @munged => $line;
            push @munged => "";

            for my $lang (@lang) {
                my $code = $lang->[0];
                my $name = $lang->[1];

                my $munge = $line;
                $munge =~ s/(?<=name)/:$code/;

                push @munged => 
                "# $lang->[0] ($lang->[1])",
                $munge;
            }

            push @munged => "";
        }
    }

    return join("\n", @munged) . "\n";
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
