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
        $options = self::getOptions( $event );
        $webDir = $options['symfony-web-dir'];
        $baseDir = getcwd();
        $legacyVoid = $baseDir . '/ezpublish_legacy';

        // linking the extensions into the legacy void
	$extensionList = array('mxdcommon','myvideo_admin','myvideo_message','myvideo_publish');
        foreach( $extensionList as $extension) {
	    if (false===file_exists($legacyVoid.'/extension/'.$extension)){ 
	            symlink($baseDir.'/src/MyVideo/extension/'.$extension, $legacyVoid.'/extension/'.$extension);
	    }
        }

        // linking the settings into the legacy void
        // linking the settings into the legacy void
        if (false === file_exists($legacyVoid . '/settings/siteaccess')) {
            symlink($baseDir . '/ezpublish/config/legacy_settings/siteaccess', $legacyVoid . '/settings/siteaccess');
        }
        if (false === file_exists($legacyVoid . '/settings/override')) {
            symlink($baseDir . '/ezpublish/config/legacy_settings/override',$legacyVoid . '/settings/override');
        }

    }

} 
