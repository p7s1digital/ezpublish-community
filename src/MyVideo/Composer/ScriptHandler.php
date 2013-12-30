<?php
/**
 * Handler class for post install task.
 *
 * User: patrik Jacoby(patrik.jacoby@magicinternet.de)
 * Date: 28.11.13
 */

namespace MyVideo\Composer;

use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as DistributionBundleScriptHandler;
use Composer\Script\CommandEvent;

class ScriptHandler extends DistributionBundleScriptHandler
{
    /**
     * @param CommandEvent $event
     */
    public static function generateSymLinks(CommandEvent $event)
    {
        $baseDir = getcwd();
        $legacyVoid = $baseDir . '/ezpublish_legacy';

        // linking the extensions into the legacy void
        $extensionList = array('mxdcommon', 'myvideo_admin', 'myvideo_message', 'myvideo_publish');
        foreach ($extensionList as $extension) {
            if (false === file_exists($legacyVoid . '/extension/' . $extension)) {
                symlink($baseDir . '/src/MyVideo/extension/' . $extension, $legacyVoid . '/extension/' . $extension);
            }
        }

        $siteaccess = $legacyVoid . '/settings/siteaccess';
        // linking the settings into the legacy void
        if (false === file_exists($siteaccess)) {
            symlink($baseDir . '/ezpublish/config/legacy_settings/siteaccess', $siteaccess);
        } else {
            // if the original directory is given, we delete it and link our own settings here
            if (true === is_dir($siteaccess)) {
                print "TRY TO DELETE " . $siteaccess . "<br />";
                exec('rm -rf ' . $siteaccess);
                symlink(
                    $baseDir . '/ezpublish/config/legacy_settings/siteaccess',
                    $siteaccess
                );
            }
        }
        if (false === file_exists($legacyVoid . '/settings/override')) {
            symlink($baseDir . '/ezpublish/config/legacy_settings/override', $legacyVoid . '/settings/override');
        }
    }

    /**
     * Creates some needed caching directories for the legacy to avoid problems with their ownership and permissions.
     * @param CommandEvent $event
     */
    public static function createCacheDirectories(CommandEvent $event)
    {
        $legacyVoid = getcwd() . '/ezpublish_legacy';
        $cacheDir = $legacyVoid . '/var/cache';
        $ezflowDir = $legacyVoid . '/var/ezflow_site/';

        if (false === file_exists($cacheDir)) {
            mkdir($cacheDir);
        }

        if (false === file_exists($ezflowDir)) {
            mkdir($ezflowDir);
        }
    }

} 
