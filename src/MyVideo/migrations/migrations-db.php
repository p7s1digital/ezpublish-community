<?php
set_include_path(__DIR__ . '/src' . PATH_SEPARATOR . get_include_path() );
chdir($_SERVER['PWD'] . '/ezpublish_legacy/');

require_once __DIR__ . '/src/DoctrineMigrations/eZPublishPackageMigration.php';
require_once( $_SERVER['PWD'] . "/ezpublish_legacy/autoload.php" );
$siteIni = eZINI::instance( 'site.ini','settings/siteaccess/ezflow_site_clean_admin', null, null, null, true);

return array(
    'driver'    => 'pdo_pgsql',
    'host'      => $siteIni->variable('DatabaseSettings', 'Server'),
    'user'      => $siteIni->variable('DatabaseSettings', 'User'),
    'password'  => $siteIni->variable('DatabaseSettings', 'Password'),
    'dbname'    => $siteIni->variable('DatabaseSettings', 'Database')
);
