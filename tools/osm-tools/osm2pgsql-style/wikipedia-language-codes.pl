#!/usr/bin/env perl

=head1 NAME

wikipedia-language-codes - Get the list of language codes currently used on Wikipedia from Special:SiteMatrix

=head1 SYNOPSIS

    # Spew out a list of Wikipedia language codes and corresponding languages 
    wikipedia-language-codes.pl > /sql/misc-data/wikipedia-languages.yml

=head1 OPTIONS

=over

=item -h, --help

Print a usage message listing all available options

=item --url

The URL to the SiteMatrix,
L<http://en.wikipedia.org/wiki/Special:SiteMatrix> by default.

=head1 AUTHOR

E<AElig>var ArnfjE<ouml>rE<eth> Bjarmason <avarab@gmail.com>

=cut

use strict;
use warnings;

use WWW::Mechanize;
use HTML::TableParser::Grid;
use Encode qw(encode decode);
use YAML::Syck qw(Dump);

use Getopt::Long;
use Pod::Usage ();

#
# Get command line options
#

Getopt::Long::Parser->new(
	config => [ qw< bundling no_ignore_case no_require_order > ],
)->getoptions(
	'h|help' => \my $help,
	'url=s' => \(my $url = 'http://en.wikipedia.org/wiki/Special:SiteMatrix'),
) or help();

help() if $help;

#
# main
#

my %matrix = parse_sitematrix();

print Dump \%matrix;

exit 0;

sub parse_sitematrix
{
    my $content = get_sitematrix();

    my $parser = HTML::TableParser::Grid->new($content);

    my %lang;

    for my $n (0 .. $parser->num_rows - 1) {
        my %row;
        @row{qw(language code)} = $parser->row($n);

        # Mark this as UTF-8
        for my $key (keys %row) {
            $row{$key} = encode('utf8', $row{$key});
        }

        next if $row{language} eq 'Total';

        $lang{$row{code}} = $row{language};
    }

    return %lang;
}

sub get_sitematrix
{
    my $mech = WWW::Mechanize->new(
        agent => $0,
    );

    $mech->get($url);

    unless ($mech->success)
    {
        die "Can't get $url";
    }

    my $content = $mech->content;

    return $content;
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
