# Generates test data for the collector and flings it at port 3815,
# written for testing a memory leak
use strict;
use IO::Socket::INET;

my $socket = IO::Socket::INET->new(
    PeerAddr => 'localhost:3815',
    Proto    => 'tcp',
) or die "Can't open socket";

# This is here because the collector keeps closing my connection, so I
# can run it as while 1; do perl generate-test-data.pl; done and it'll
# send the correct serial
my $serial = do { local (@ARGV) = 'serial'; <> };

while (1)
{
    $serial++;
    open my $fh, ">", 'serial' or die $!;
    print $fh $serial;
    close $fh;

    my $project = 'iswiki';
    my $count   = 1;
    my $bytes   = 10 + int rand 2**8;
    my $page    = 'Foo';

    my $msg = sprintf "%d %s %d %s\n", $serial, 'iswiki', $bytes, $page;

    print $msg;
    $socket->print($msg);
}
