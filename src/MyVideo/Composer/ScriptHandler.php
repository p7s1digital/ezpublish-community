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
        $extensionList = ['mxdcommon','myvideo_admin','myvideo_message','myvideo_publish'];
        foreach( $extensionList as $extension) {
            symlink($legacyVoid.'/extension', $baseDir.'/src/MyVideo/extension/'.$extension);
        }

        // linking the settings into the legacy void
        symlink($legacyVoid.'/settings', $baseDir.'ezpublish/config/settings/siteaccess');
        symlink($legacyVoid.'/settings', $baseDir.'ezpublish/config/settings/override');

        // and finally linking the various needed legacy directories into the web folder
        symlink($webDir,$legacyVoid.'/design');
        symlink($webDir,$legacyVoid.'/extension');
        symlink($webDir,$legacyVoid.'/share');
        symlink($webDir,$legacyVoid.'/var');
    }

} 