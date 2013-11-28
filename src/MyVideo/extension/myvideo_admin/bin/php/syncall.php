#!/usr/bin/env php
<?php
/**
 * Goes through all objects and (re)syncs the data to the redis storage
 *
 * example 1 - sync all objects
 * $ php extension/myvideo_admin/bin/php/syncall.php --classes=*
 *
 * example 2 - sync only the given content classes
 * $ php extension/myvideo_admin/bin/php/syncall.php --classes=myvideo_homepage,myvideo_menu
 *
 * example 3 - sync only the given id(s)
 * $ php extension/myvideo_admin/bin/php/syncall.php --ids=622,624
 */

set_time_limit(0);

require 'autoload.php';

$cli = eZCLI::instance();

$script = eZScript::instance(array('description' => ("MyVideo sync updater.\n\n" .
    "Goes through all objects and (re)syncs the data to the redis storage" .
    "\n" .
    "syncall.php"),
    'use-session' => true,
    'use-modules' => true,
    'use-extensions' => true));

$script->startup();

$options = $script->getOptions("[classes:][dryrun][ids:]",
    "",
    array('classes' => "sync only provided classes\nuse classes=* to sync all classes",
        'ids' => "sync only provided ids",
        'dryrun' => "dryrun, do not actually (re)sync objects",
    ));
$script->initialize();

// run as admin user ...
$user = eZUser::fetchByName('admin', true);
if (!$user) {
    throw new Exception('not a avalid user');
} else {
    eZUser::setCurrentlyLoggedInUser($user, $user->attribute('id'));
}

$script->setIterationData('.', '~');

$classes = $options['classes'] ? $options['classes'] : false;
$ids = $options['ids'] ? $options['ids'] : false;
$dryrun = $options['dryrun'] ? true : false;

if ($dryrun) {
    $cli->output("DRYRUN - objects are NOT (re)synced");
}


$validObjectIds = array();
$validClassIds = array();
$validClassIdentifier = array();
$flagShowAll = false;
$switchMode = 'classes';

if ($classes == '*') {

    $flagShowAll = true;

} elseif ($classes) {

    $classesProvided = array_unique(explode(',', $classes));

    foreach ($classesProvided as $classProvided) {

        $contentClassId = eZContentClass::classIDByIdentifier($classProvided);
        if ($contentClassId) {
            array_push($validClassIds, $contentClassId);
            array_push($validClassIdentifier, $classProvided);
        } else {
            $cli->output("INVALID class_identifier: $classProvided");
        }
    }
} elseif ($ids) {

    $idsProvided = array_unique(explode(',', $ids));
    $validObjectIds = array_filter(array_map('intval', $idsProvided));
    $switchMode = 'ids';

} else {
    $script->shutdown(1, "STOP EXECUTION - The --classes OR --ids option is mandatory");
}


eZExecution::registerShutdownHandler();

$def = eZContentObject::definition();
$conds = array(
    'status' => eZContentObject::STATUS_PUBLISHED
);


if (count($validClassIds) > 0) {
    $conds['contentclass_id'] = array($validClassIds);
} elseif ($flagShowAll) {
    // do nothing else
    $validClassIdentifier = array('* => all classes (content types)');
} elseif (count($validObjectIds)) {
    $conds['id'] = array($validObjectIds);
} else {
    $script->shutdown(1, "STOP EXECUTION No valid classes provided");
}


$cli->output("Starting object re-syncing");
$count = eZPersistentObject::count($def, $conds, 'id');

if ($switchMode == 'classes') {
    $cli->output('Classes (ContentTypes) to sync: ' . join(', ', $validClassIdentifier));
}

if ($switchMode == 'ids') {
    $cli->output('Ids (Contentobject ID) to sync: ' . join(', ', $validObjectIds));
}

$cli->output("Number of objects to sync: $count");

$length = 50;
$limit = array('offset' => 0, 'length' => $length);

$script->resetIteration($count);

do {
    // clear in-memory object cache
    eZContentObject::clearCache();

    $objects = eZPersistentObject::fetchObjectList($def, null, $conds, null, $limit);

    foreach ($objects as $object) {
        if (!$dryrun) {
            // TODO: improvement - show the result of render action (success, skip, error)
            MyVideoRenderer::render($object);
        }
        $script->iterate($cli, true);
    }

    $limit['offset'] += $length;

} while (count($objects) == $length);


$cli->output();
$cli->output("done");

$script->shutdown();
