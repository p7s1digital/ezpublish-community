#!/usr/bin/perl
# Deploy Helper Script
# Reads some ini files (default.ini / environment.ini / overwrite.ini) and evaluate template files.
# (hes) 19.03.2013

use Config::IniFiles;
use Template;
use Getopt::Long;
#use Data::Dumper;

my $defaultFile = '';
my $envFile = '';
my $overFile = '';
my $templateRoot = '/';
my @templates = (); # extension .tt2
my $help = 0;

$result = GetOptions("default|d=s" => \$defaultFile,
                     "environment|e=s" => \$envFile,
                     "overwrite|o=s" => \$overFile,
                     "templates|t=s" => \@templates,
                     "help|h" => \$help
                     );
@templates = split(/,/,join(',',@templates));   # handle comma separated list

if ($help || !$result) {
    print "$0\n\t--default=def.ini (default ini file)\n\t".
        "--environment=env.ini (environment ini file)\n\t".
        "--overwrite=over.ini (optional local overwrite ini file)\n\t".
        "--template=a.tt2,b.tt2,... (comma seperated list of template files)\n\t".
        "--help this text\n\n".
        "Templates must be referenced by an absolute path.\n".
        "Output files will be stored in the same directory without the .tt2 extension.\n";
    exit 1;
}

my $cfg;
my $def;

print "Default ini File \"$defaultFile\" ";
$def = Config::IniFiles->new( -file => $defaultFile, -default => 'default'  )  or die @Config::IniFiles::errors;
print "loaded.\n";

if ($overFile) {
    # there is an overwritefile which will be loaded after the environment file
    print "Environment ini File \"$envFile\" ";
    my $ov = Config::IniFiles->new( -file => $envFile, -default => 'default'  )  or die @Config::IniFiles::errors;
    print "loaded.\n";
    print "Overwrite ini File \"$overFile\" ";
    tie %cfg, 'Config::IniFiles', ( -file => $overFile, -import => $ov ) or die @Config::IniFiles::errors;
    print "loaded.\n";
} else {
    print "Environment ini File \"$envFile\" ";
    tie %cfg, 'Config::IniFiles', ( -file => $envFile, -import => $def ) or die @Config::IniFiles::errors;
    print "loaded.\n";
}

my $output = '';
my $tt = Template->new(INCLUDE_PATH => $templateRoot, ABSOLUTE => 1, EVAL_PERL => 1) or die $Template::ERROR, "\n";

foreach $template (@templates) {
    if ($template =~ m/^(.*).tt2$/) {
        my $outFile = $1;
        my $output = '';
        print "Output file \"$outFile\"...";

        $tt->process($template, \%cfg, \$output) or die $tt->error(), "\n";

        open(OUT, ">$outFile") || die "$!: Can't write to file $outFile\n";
        print OUT $output;
        close(OUT);
        print "written.\n";
    } else {
        die "Template \"$template\" should have extension .tt2\n";
    }
}

exit 0;