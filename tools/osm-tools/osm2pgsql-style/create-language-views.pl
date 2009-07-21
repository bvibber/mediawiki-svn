#!/usr/bin/env perl

=head1 NAME

create-language-views.pl - Create the views needed to render l18n-enabled maps with mapnik

=head1 SYNOPSIS

    perl wikipedia-language-codes.pl > /sql/misc-data/wikipedia-languages.yml
    perl create-language-views.pl --languages /sql/misc-data/wikipedia-languages.yml --psql-user gis --psql-db gis > create_views.sql
    perl create-language-views.pl --languages /sql/misc-data/wikipedia-languages.yml --psql-user gis --psql-db gis --delete > delete_views.sql
    psql -U gis -d gis < create_views.sql

=head1 OPTIONS

=over

=item -h, --help

Print a usage message listing all available options

=item --languages

A YAML file to read languages from, e.g. F</sql/misc-data/wikipedia-languages.yml>

=item --psql-user

The PostgreSQL user to use.

=item --psql-db

The PostgreSQL database to use.

=item --delete

Create SQL to delete the views and associated data.

=head1 LINKS

L<http://wiki.openstreetmap.org/wiki/Regionalisedmap>

=head1 AUTHOR

E<AElig>var ArnfjE<ouml>rE<eth> Bjarmason <avarab@gmail.com>

=cut

use feature ':5.10';
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
    'psql-user=s' => \my $psql_user,
    'psql-db=s' => \my $psql_db,
    'delete' => \my $delete,
    'style=s' => \my $style,
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

if (not defined $psql_user)
{
    warn "--psql-user must be supplied";
    help();
}

if (not defined $psql_db)
{
    warn "--psql-db must be supplied";
    help();
}

#
# Config
#

# The tables we're creating views for
my @tables = split_query( "select table_name from information_schema.tables where table_name ~ '^planet_osm_(point|line|polygon|roads)';" );

if (not @tables) {
    die "There were no tables beginning with 'planet_osm' in your database, or perhaps the database connection failed";
}

# Columns in those tables
my %columns;

for my $table (@tables) {
    my @columns = split_query( "select column_name from information_schema.columns where table_name = '$table';" );

    $columns{$table} = [ @columns ];
}

# Our languages
my %languages = %{ LoadFile($languages) };
my @languages = sorted_languages(%languages);

#
# main
#

my %created_views;
for my $language (@languages)
{
    my $code = $language->[0];
    my $name = $language->[1];

    say "--";
    say "-- Begin views for $code ($name)";
    say "--";

    for my $table (@tables) {
        my @munged_columns = munged_columns($code, @{ $columns{$table} });

        my $view_name = "view_${table}_lang_${code}";

        if ($delete) {
            say "DROP VIEW \"$view_name\";";
        } else {
            say "CREATE VIEW \"$view_name\" as";
            say "    SELECT";
            say join ",\n", map { "        $_" } @munged_columns;
            say "FROM $table;";
            say "";
        }

        push @{ $created_views{ $table} } => $view_name;
    }

    say "--";
    say "-- End views for $code ($name)";
    say "--";
    say "";
    say "";
}

while (my ($table, $views) = each %created_views) {
    my @views = @$views;

    my $type;
    given ($table) {
        when ("planet_osm_point")   { $type = "POINT" }
        when ("planet_osm_line")    { $type = "LINESTRING" }
        when ("planet_osm_polygon") { $type = "POLYGON" }
        when ("planet_osm_roads")   { $type = "LINESTRING" }
    }

    say "";
    say "--";
    say "-- Views of $table ($type)";
    say "--";
    say "";

    for my $view (@views) {
        if ($delete) {
            say qq[DELETE FROM geometry_columns WHERE f_table_catalog = '' AND f_table_schema = 'public' AND f_table_name = '$view' AND f_geometry_column = 'way' AND coord_dimension = 2 AND srid = 900913 AND type = '$type';];
        } else {
            say qq[INSERT INTO geometry_columns VALUES ('', 'public', '$view', 'way', 2, 900913, '$type');];
        }
    }
}

exit 0;

sub munged_columns
{
    my ($code, @columns) = @_;
    my @ret;

    for my $column (@columns) {
        if ($column !~ /^name/) {
            push @ret => qq["$column"];
        } else {
            if ($column =~ /^name$/) {
                # We only want one name column
                push @ret => qq[case when "name:$code" is not null then "name:$code" else name end as name];
            } else {
                # Drop all other name:$whatever columns
            }
        }
    }

    return @ret;

}

sub split_query
{
    my $query = shift;

    map { /(\S+)/; $1 } split /^/, do_query($query);
}

sub do_query
{
    my $query = shift;

    my $out = qx[ echo "$query" | psql -t -A -U $psql_user -d $psql_db ];
    return $out;
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
