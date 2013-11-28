<?php
/**
 * This class contains complex validation rules for content types.
 * Because eZ publish can validate only on attribute level.
 *
 * HINT: if there will be more complex validation requirements in the future it might be a good idea
 * to use separate validation classes per content type.
 *
 * By now this is a very simple approach using a method for each content type which needs complex validation.
 */

class MyVideo_AdminHandler extends eZContentObjectEditHandler
{
    const CHECKBOX_SAVE_WITH_ERRORS = '__saveWithErrors';

    /**
     * handle input validation.
     *
     * @param ezHTTPTool                    $http
     * @param                               $module
     * @param                               $class
     * @param                               $object
     * @param eZContentObjectVersion        $version
     * @param ezContentObjectAttribute[]    $contentObjectAttributes
     * @param                               $editVersion
     * @param                               $editLanguage
     * @param                               $fromLanguage
     * @param                               $validationParameters
     *
     * Result with warnings are expected in the following format:
     * array( 'is_valid' => false, 'warnings' => array( array( 'text' => 'Input parameter <some_id> must be an integer.' ) ) );
     *
     * @return array
     */
    function validateInput($http, &$module, &$class, $object, &$version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage, $validationParameters)
    {
        // default return value
        $result = array('is_valid' => true, 'warnings' => array());

        // Skip-isRequired is set, when f.e. editing a content object and adding a relation to another object.
        // To be able to leave the edit page to move on the content-selection-screen eZ sets this flag, to tell the validation
        // to let things pass even though there would be an error in the validation.
        $skipIsRequired = isset($validationParameters['skip-isRequired']) ? $validationParameters['skip-isRequired'] : false;

        if (!$skipIsRequired) {
            $postParameters = $http->attribute('post');

            // Clone and then update the attributes with the data we just received.
            // So we ensure to validate against the current user input
            $attributesToBeUsed = $this->cloneAndUpdateAttributesWithUserInput($http, $contentObjectAttributes);

            // do the validation
            $validationResult = MyVideoCustomValidation::getInstance()->validate($object, $attributesToBeUsed);

            if ($validationResult) {
                // If the validation is ok, or if we only have warning and the self::CHECKBOX_SAVE_WITH_ERRORS checkbox is checked,
                // then we act as if validation was successful
                $saveWithWarnings = isset($postParameters[self::CHECKBOX_SAVE_WITH_ERRORS]);

                if ($validationResult->isOk() ||
                    ($validationResult->isWarning() && $saveWithWarnings)
                ) {
                    $result = array('is_valid' => true, 'warnings' => array());
                } else {
                    // else transform the result so ez understands it
                    $result = array('is_valid' => false, 'warnings' => array());

                    foreach ($validationResult->getFacts() as $fact) {
                        $result['warnings'][] = array('text' => $fact);
                    }

                    // if we have just warnings then we show the '__saveWithWarnings' checkbox
                    if ($validationResult->isWarning()) {
                        $checkWithErrors = self::CHECKBOX_SAVE_WITH_ERRORS;
                        $result['warnings'][] = array(
                            'text' => '<input name="' . $checkWithErrors . '" id="' . $checkWithErrors . '" type="checkbox">' .
                            '<label style="display: inline;" for="' . $checkWithErrors . '">Mit Warnungen speichern</label>'
                        );
                    }
                }
            }

            // do additional validation
            $now = new DateTime();
            $start = self::getDate($http->postVariable('workflow-start'));
            $end = self::getDate($http->postVariable('workflow-end'));

            if (($start !== false) && ($start < $now)) {
                $result['is_valid'] = false;
                $result['warnings'][] = array('text' => ezpI18n::tr('extension/xrowworkflow', 'Select a publication date in the future.'));
            }
            if (($end !== false) && ($end < $now)) {
                $result['is_valid'] = false;
                $result['warnings'][] = array('text' => ezpI18n::tr('extension/xrowworkflow', 'Select a expiry date in the future.'));
            }
            if (($start !== false) && ($end !== false) && ($end < $start)) {
                $result['is_valid'] = false;
                $result['warnings'][] = array('text' => ezpI18n::tr('extension/xrowworkflow', 'Select a expiry date newer then the publication date.'));
            }
        }


        return $result;
    }

    /**
     * @param ezHTTPTool                    $http
     * @param ezContentObjectAttribute[]    $attributes
     *
     * @return ezContentObjectAttribute[]
     */
    private function cloneAndUpdateAttributesWithUserInput($http, $attributes)
    {
        $attributesToBeUsed = array();

        foreach ($attributes as $attribute) {
            $clonedAttribute = clone($attribute);
            // update the cloned attribute with the data from the request
            $clonedAttribute->fetchInput($http, 'ContentObjectAttribute');
            $attributesToBeUsed[] = $clonedAttribute;
        }

        return $attributesToBeUsed;
    }

    /**
     * Return Date
     *
     * @param $args
     * @return bool|DateTime
     */
    public function getDate($args)
    {
        if (empty($args['date'])) {
            return false;
        }
        $date = new DateTime($args['date']);
        $date->setTime($args['hour'], $args['minute']);

        return $date;
    }

    /**
     * @param ezHTTPTool                    $http
     * @param                               $module
     * @param                               $class
     * @param ezContentObject               $object
     * @param ezContentObjectVersion        $version
     * @param ezContentObjectAttribute[]    $contentObjectAttributes
     * @param                               $editVersion
     * @param                               $editLanguage
     * @param                               $fromLanguage
     */
    public function fetchInput($http, &$module, &$class, $object, &$version, $contentObjectAttributes, $editVersion, $editLanguage, $fromLanguage)
    {
        $start = self::getDate($http->postVariable('workflow-start'));
        $end = self::getDate($http->postVariable('workflow-end'));

        $row = array('contentobject_id' => $object->ID);
        if ($start) {
            $row['publish'] = $start->getTimestamp();
        }
        if ($end) {
            $row['expire'] = $end->getTimestamp();
        }
        $obj = new xrowworkflow($row);
        $obj->store();

        $contentObject = array('contentobject_id' => $object->ID);
        $contentObject['ivw_agofcode'] = $http->postVariable('ivw_agofcode');
        $contentObject['ivw_path'] = $http->postVariable('ivw_path');
        $contentObject['ga_tvbrand'] = $http->postVariable('ga_tvbrand');
        $contentObject['ga_channel'] = $http->postVariable('ga_channel');
        $contentObject['custom_pixels'] = $http->postVariable('custom_pixels');

        $tracking = new myvideoTracking($contentObject);
        $tracking->store();
        unset($contentObject);

        $contentObject = array('contentobject_id' => $object->ID);
        $contentObject['title'] = $http->postVariable('metadata_title');
        $contentObject['keywords'] = $http->postVariable('metadata_keywords');
        $contentObject['description'] = $http->postVariable('metadata_description');
        $contentObject['robots'] = $http->postVariable('metadata_robots');
        $contentObject['image_url'] = $http->postVariable('metadata_image_url');

        $metaData = new myvideoMetaData($contentObject);
        $metaData->store();
        unset($contentObject);

        // simad configuration
        $contentObject = array('contentobject_id' => $object->ID);
        $contentObject['channel'] = $http->postVariable('channel');
        $contentObject['subchannel1'] = $http->postVariable('subchannel1');
        $contentObject['subchannel2'] = $http->postVariable('subchannel2');
        $contentObject['dfpsite'] = $http->postVariable('dfpsite');
        $contentObject['dfpzone'] = $http->postVariable('dfpzone');

        $simAdConfig = new myvideoSimAdConfig($contentObject);
        $simAdConfig->store();
        unset($contentObject);

        if (is_object($module) && $module->isCurrentAction('Store')) {
            if (eZOperationHandler::operationIsAvailable('myvideopublish_preview')) {
                eZOperationHandler::execute('myvideopublish', 'preview', array('object_id' => $object->attribute('id'),
                        'version' => $version->attribute('version')
                    )
                );
                $module->addHook('action_check', array($GLOBALS['MyVideoPreviewTypeAsGlobal'], 'renderPreviewContentObject'), 2, true, true);
            } else {
                MyVideoPublishOperationCollection::preview($object->attribute('id'), $version->attribute('version'), $module);
            }
        }
    }

    /**
     * @param int                       $contentObjectID
     * @param ezContentObjectVersion    $version
     */
    public function publish($contentObjectID, $version)
    {
        $cov = eZContentObjectVersion::fetchVersion($version, $contentObjectID);
        if ($cov instanceof eZContentObjectVersion) {
            $co = $cov->attribute('contentobject');
            if ($co && $co->attribute('class_identifier') == 'event') {
                $dm = $cov->dataMap();
                if (isset($dm['publish']) && $dm['publish']->hasContent()) {
                    $time = $dm['publish']->attribute('data_int');
                    $co->setAttribute('published', $time);
                    $co->store();
                }
            }
        }
        $workflow = xrowworkflow::fetchByContentObjectID($contentObjectID);
        if ($workflow instanceof xrowworkflow) {
            $workflow->check();
        }
    }
}
