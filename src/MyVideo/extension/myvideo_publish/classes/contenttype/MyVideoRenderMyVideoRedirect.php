<?php
/**
 * Render an eZPublish contentType myvideo_redirect
 */

class MyVideoRenderMyVideoRedirect extends MyVideoRendererBase implements MyVideoRendererInterface
{
    const HTTP_REDIRECT = 301;
    const HTTP_NOT_FOUND = 404;
    const HTTP_GONE = 410;

    const DEFAULT_STATE = self::HTTP_REDIRECT;


    public function fetchKey()
    {
        return $this->getAttributeValue('key');
    }


    /**
     * create a simplified data array
     * @return array|mixed|null
     * @throws Exception
     */
    public function render()
    {
        $standard = parent::render();

        $data = array();
        $data['type'] = $standard['type'];

        // TODO might be useful to introduce a helper method
        if (array_key_exists(0, $standard['fields']['status']['content']['selected_values'])) {

            $data['status'] = $standard['fields']['status']['content']['selected_values'][0];
        } else {

            $data['status'] = self::DEFAULT_STATE;
        }

        if (!self::isValidState($data['status'])) {
            throw new Exception('can not handle http state ' . $data['status']);
        }


        if ($data['status'] == self::HTTP_REDIRECT) {
            // TODO use getAttributeValue?
            $node_id = $standard['fields']['target_node_id']['content'];

            // TODO handle invalid or missing node_id
            $node = eZContentObjectTreeNode::fetch($node_id);

            if ($node instanceof eZContentObjectTreeNode) {

                $contentObject = $node->object();

                $languageList = $contentObject->availableLanguages();

                // ASSERT redirect is never translated
                if (count($languageList) > 1) {
                    // TODO FIX THIS
                    // throw new Exception('redirect is ambiguous - translation is not allowed here'.serialize($languageList));
                }
                $language = $languageList[0];

                $renderer = MyVideoRenderFactory::getInstance($contentObject);
                $renderer->setLanguage($language);
                $key = $renderer->fetchKey();
                $data['new_key'] = $key;
            } else {
                // TODO handle or log error
                $data['error'] = 'target node not found';
                $data['status'] = self::HTTP_NOT_FOUND;
            }
        }

        return $data;
    }


    /**
     * return array of valid states
     *
     * @return array
     */
    public static function getValidStates()
    {
        return array(self::HTTP_REDIRECT, self::HTTP_NOT_FOUND, self::HTTP_GONE);
    }

    /**
     * check if $n is a valid state
     *
     * @param integer $n
     * @return boolean
     */
    public static function isValidState($n)
    {
        return in_array($n, self::getValidStates());
    }
}
