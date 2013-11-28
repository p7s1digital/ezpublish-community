<?php
/**
 * sets the target where the user is redirected to after an object is edited
 */

// get the view parameters ...
$pars = $Params['Parameters'];
$oid = intval(array_shift($pars));

// get the node and its url-alias for the target uri ...
$redirectAfterURI = '/' . join('/', $pars);

// store the target uri in user session ...
$http = eZHTTPTool::instance();
$http->setSessionVariable('RedirectURIAfterPublish', $redirectAfterURI);
$http->setSessionVariable('RedirectIfDiscarded', $redirectAfterURI);

// define the required parameters ...
$ObjectID = $oid;
$EditLanguage = 'ger-DE';
$selectedVersion = 'current';

// redirect to the edit view of the content module ...
return $Module->redirect('content', 'edit', array($ObjectID, $selectedVersion, $EditLanguage));

eZExecution::cleanExit();
