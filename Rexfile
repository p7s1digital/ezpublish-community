use strict;
use warnings;

use Rex -feature => [qw/sudo_without_locales sudo_without_sh exit_status/];

=head1 NAME

Rexfile - (R)?ex Deployment Script for myvideo nextlevel ezpublish

=head1 AUTHORS

=over 4

=item Dominik Schulz <dominik.schulz@magicinternet.de>

=back

=head1 USAGE

$> rex -E {integration,staging,live} deploy --tarball=myvideo-ezpublish-ab12cd34.tar.gz

=head1 CONTROL FLOW

Task 'deploy' tests for a (apparently) valid tarball, calls deploy
(see Rex::Apache::Deploy). deploy uses the generate_deploy_directory sub to
extract the target directory below $deploy_to from the filename.

Then deploy will upload, extract and symlink the tarball.

Afterwards the tasks 'test' and 'reload' are pulled in as a dependency.

=head2 IN DETAIL

=over 4

=item 1. Configuration for Rex::Apache::Deploy module

Rex::Apache::Deploy module switches automatically to the newley deployed version.
But this is not what we want to do. Before switching to production we want to run 
some tests to verify the installation.
This is why we are setting document_root to a temporary directory

=item 2. deploy $tarball

Tarball is uploaded to the server and extracted into the directory defined by I<deploy_to>.
Also we need to change the filesystem permissions so that the www-data user can write into
these directories.

=item 3. create environment dependent configs

After uploading the configuration files gets generated with the help of ini2template.pl script.
This script uses /etc/myvideo/default.ini which is deployed through puppet.

The cause to use puppet for this file is to have an easier way to use different default.ini files
on different hosts.

=item 4. database migrations

Database migrations are currently enabled.

=item 5. creating symlinks and publishing content to redis

After the deployment we have to run some ezpublish scripts that will create some symlinks and publish 
the content to redis.

=item 6. run integrity checks

This task is doing some basic tests for example if the file permissions are right.

=item 7. remove / pack old deployed versions

This is a cleanup job. The purpose of this job is to cleanup the I<deploy_to> directory. 
Only the last 10 versions will be kept.

=item 8. activate newly deployed version

If all of the above didn't failed the new version is put to production.

=item 9. restart nginx and php5-fpm

The last thing is to restart nginx and php5-fpm. php-fpm needs to be restarted so it loads
the new application code.

=cut

use Rex::Transaction;

use Rex::Apache::Build;
use Rex::Apache::Deploy 'Symlink';

# Global configuration

parallelism(5); # deploy with 5 parallel tasks

my $web_base = '/srv/www';
my $component_name = 'ezpublish';
my $component_base = $web_base . q{/} . $component_name;
my $component_current = $component_base . q{/current};
my $component_current_temp = $component_base . q{/current.temp};
my $component_config  = $component_base . q{/config};
my $component_docroot = $component_current . q{/web};

# define default frontend group containing only int-ezpublish
group ezpublish => 'int-ezpublish';

# ACLs for cache directory
# If you need to define more acls, just extend this array.
# :rwx gets expanded to :rwX
# you have to use the verbose definitions, because this array is also used
# by the test task to verify the acls of a directory.
# so u:someuser:rwx won't work.
my @acls = qw/
   default:user:myvideo:rwx
   user:myvideo:rwx
   default:user:www-data:rwx
   user:www-data:rwx
/;

# Read additional configuration from a file
my $cfg_file = $ENV{'REXCONFIG'} || '/var/lib/jenkins/.rexconfig';
if(-e $cfg_file) {
   # should set sudo_password, public_key, private_key and some groups
   do $cfg_file;
}


# Metadata
# Configuration for Rex::Apache::Deploy module
# Here we're using a temporary directory, so that we can switch
# the document_root after the tests.
deploy_to $component_base;
document_root $component_current_temp;

# as default we do a "source /etc/profile" before a 'run()'
source_global_profile TRUE;

# Methods
# This function creates the deployment directory out of the tarball name
generate_deploy_directory sub {
   my $file = shift;

   if($file =~ m/-([a-f0-9]+-\d+)\.tar\.gz$/) {
      return $1;
   }

   return 'default';
};

# Tasks
desc "Deploy a new myvideo ezpublish version to all servers.";
task 'deploy', group => 'ezpublish', sub {
   my $params = shift;

   # get the tarball parameter out of the cli parameters
   #    rex deploy --tarball=tar-ball-name.tar.gz
   my $tarball = $params->{'tarball'} || die('Need Tarball arg!');
   die('Need valid Tarball at '.$tarball) unless -e $tarball;

   my $deploy_dir = get_deploy_directory_for($tarball);

   # upload and extract the tarball to the systems
   deploy $tarball;

   # create logs directory if not existent
   # currently this directory is missing in the tarball.
   mkdir "$deploy_dir/logs";

   # config is created by puppet in /srv/www/ezpublish/config/ezpublish.yml
   # db is created by puppet (ezpublish_pgsql_server service)

   # logs dir from list removed (/srv/www/ezpublish/logs: Operation not permitted) (workaround)
   # Set the filesystem ACLs so that myvideo and www-data can write to it.

   # here we need to set permissions for myvideo and www-data user.
   # the deployment is done by ,,myvideo'' but the webserver is running under ,,www-data''
   Rex::Logger::info("Setting permissions for myvideo and www-data user.");
   my $acl_string = join(",", @acls);
   $acl_string =~ s/:rwx/:rwX/g;   # settings executable bit only on directories

   foreach my $dir (qw(logs ezpublish/logs ezpublish/cache ezpublish/config)) {
      Rex::Logger::info("   $deploy_dir/$dir");
      say "" . run "cd $deploy_dir/$dir; setfacl -Rm $acl_string $deploy_dir/$dir";
      is($? == 0, "   ACLs for $deploy_dir/$dir");

      sudo "/bin/chown -R www-data:www-data $deploy_dir/$dir";
      sudo "/bin/chmod -R 775 $deploy_dir/$dir";
   }

   # some permissions under ezpublish_legacy directory
   foreach my $dir (qw(design extension settings var)) {
      Rex::Logger::info("   $deploy_dir/ezpublish_legacy/$dir");
      say "" . run "cd $deploy_dir/ezpublish_legacy/$dir; setfacl -Rm $acl_string $deploy_dir/ezpublish_legacy/$dir";
      is($? == 0, "   ACLs for $deploy_dir/ezpublish_legacy/$dir");
      sudo "/bin/chown -R www-data:www-data $deploy_dir/ezpublish_legacy/$dir";
      sudo "/bin/chmod -R 775 $deploy_dir/ezpublish_legacy/$dir";
   }
   sudo "/bin/chown -R www-data:www-data $deploy_dir/ezpublish_legacy";

   # now we're creating the configuration files
   Rex::Logger::info("Creating configuration files out of /etc/myvideo/default.ini");
   say "" . run "/usr/bin/perl ".$deploy_dir.'/bin/ini2template.pl -e /etc/myvideo/default.ini -t '.$deploy_dir.'/ezpublish_legacy/settings/override/environment.ini.append.php.tt2';
   is(is_file("$deploy_dir/ezpublish_legacy/settings/override/environment.ini.append.php"), "Creating configuration file");

   # Some debug output
   say "" . run 'echo "-------"; cat /etc/myvideo/default.ini';
   say "" . run 'echo "-------"; cat '.$deploy_dir.'/ezpublish_legacy/settings/override/environment.ini.append.php';

   Rex::Logger::info("Running database migrations. (deactivated for now)");
   sudo "chmod -R 722 $deploy_dir/bin/migrations.sh";
   run "cd $deploy_dir; ./bin/migrations.sh migrations:migrate --no-interaction";
   if($? != 0) {
      die("Migration failure!");
   }

   Rex::Logger::info("Publishing assets");
   # publish assets
   # these are some internal ezpublish scripts creating some symlinks
   say "" . run "cd $deploy_dir && php ezpublish/console assets:install --symlink web";
   is($? == 0, "Running assets:install");
   say "" . run "cd $deploy_dir && php ezpublish/console ezpublish:legacy:assets_install --symlink web";
   is($? == 0, "Running ezpublish:legacy:assets_install");
   say "" . run "cd $deploy_dir/ezpublish_legacy/var && mkdir -p ezflow_site && cd ezflow_site && ln -s /srv/www/ezpublish/assets/ezflow_site/storage";
   if($? != 0) {
      cp $tarball, "/tmp/failed-cms-deployment.tar.gz";
   }
   is($? == 0, "Creating links for ezflow_site storage");

   # sync all content to redis server again
   say "" . run "cd $deploy_dir/ezpublish_legacy/ && /usr/bin/php extension/myvideo_admin/bin/php/syncall.php --classes=*";
   is($? == 0, "Syncing all content to redis server again");

   # add dependencies on the tasks "test" and "reload"
   needs "test";

   # remove / pack old deployed versions, cleanup of deploy_to directory.
   # keep only the last 10 versions.
   Rex::Logger::info("remove / pack old deployed versions");
   say "" . run '/bin/bash '.$deploy_dir.'/bin/archive_old_versions.sh /srv/www/ezpublish ezpublish/cache';
   if ($?) {
      die("Error in archive_old_versions.sh!");
   }

};

desc "Switch Version";
task "switch_version", group => "ezpublish", sub {
   my $param = shift;

   # on failure, roll back to the old version
   my $old_live = get_live_version;
   on_rollback {
      say "Rolling back to $old_live\n";
      switch_to_version($old_live);
      reload();
   };

   my $tarball = $param->{'tarball'} || die('Need Tarball arg!');
   my $deploy_dir = get_deploy_directory_for($tarball);

   # activate deployed version!! remove temporary link
   Rex::Logger::info("Link new current $deploy_dir => $component_current");

   # @see Metadata
   # After the tests ran successfull we can switch the document_root to the new version
   symlink($deploy_dir, $component_current);
   # and remove the temporary link
   unlink($component_current_temp);

   # At last we have to restart nginx and php5-fpm. So that php5-fpm reloads the application.
   needs "reload";
};



task 'test', group => 'ezpublish', sub {
   my $params = shift;

   Rex::Logger::info("Quick install validation");
   my $tarball = $params->{'tarball'} || die('Need Tarball arg!');
   my $deploy_dir = get_deploy_directory_for($tarball);

   # Check if the deployment directory exists
   is(is_dir($deploy_dir), 'Created deploy directory at '.$deploy_dir);

   foreach my $dir (qw(logs ezpublish/logs ezpublish/cache ezpublish/config)) {
      my $full_dir = "$deploy_dir/$dir";
      my %stat = stat($full_dir);
      is($stat{uid} == 33, 'changed UID of '.$full_dir.' to www-data (33)! Current value: '.$stat{'uid'});
      is($stat{gid} == 33, 'changed GID of '.$full_dir.' to www-data (33)! Current value: '.$stat{'gid'});
      is($stat{mode} eq "0775", 'changed mode of '.$full_dir.' to 0775! Current value: '.$stat{'mode'});
   }

   foreach my $dir (qw(design extension settings var)) {
      my $full_dir = "$deploy_dir/ezpublish_legacy/$dir";
      my %stat = stat($full_dir);
      is($stat{uid} == 33, 'changed UID of '.$full_dir.' to www-data (33)! Current value: '.$stat{'uid'});
      is($stat{gid} == 33, 'changed GID of '.$full_dir.' to www-data (33)! Current value: '.$stat{'gid'});
      is($stat{mode} eq '0775', 'changed mode of '.$full_dir.' to 0775! Current value: '.$stat{'mode'});
   }
};

task 'reload', group => 'ezpublish', sub {
   Rex::Logger::info("Restart nginx and php5-fpm");

   # sourcing the global profile would break our limited sudo rights
   sudo TRUE;
   source_global_profile FALSE;
   run '/etc/init.d/php5-fpm restart';
   run '/etc/init.d/nginx restart';
   source_global_profile TRUE;
   sudo FALSE;
};

# this is an example task if transactions are needed again.
# than you can create a on_callback {} code block inside the deploy task.
task "rollout", sub {
   # open a new transaction
   transaction {
      # execute deploy task
      do_task [qw/deploy switch_version/];
   };
};

# simple functions for the tests. This will print out the test and if the test fails
# it cancels the execution.
sub is {
   my ($should, $msg) = @_;

   if(! $should) {
      Rex::Logger::info("Failed: $msg", "warn");
      die($msg);
   }
   else {
      Rex::Logger::info($msg);
   }
}

"MyVideo"; # be safe, return true value.
