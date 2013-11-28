<?php
/**
 * Render an eZPublish contentType myvideo_menu
 */

class MyVideoRenderMyVideoMenu extends MyVideoRendererBase implements MyVideoRendererInterface
{
    /**
     * render content object
     */
    public function render()
    {
        $node = $this->ContentObject->attribute('main_node');
        $data = array();
        $data['type'] = 'menu';
        $this->copyFieldContent('title', $data);
        $data['children'] = $this->renderLevel($node);
        $data['welcomeScreen'] = $this->renderWelcomeScreen();

        return $data;
    }

    private function renderWelcomeScreen()
    {
        $data = array();
        $data['type'] = 'welcomeScreen';
        $this->copyFieldContent('welcome_title', $data);
        $this->copyFieldContent('url', $data);
        $this->copyFieldContent('description', $data);
        $this->copyFieldContent('image', $data, true, array('src'));
        $this->copyFieldContent('start', $data);
        $this->copyFieldContent('end', $data);
        $this->copyFieldContent('display_frequency', $data);

        // in this ez publish class the title attribute is already in use, but we want to get a beautiful json
        if (isset($data['welcomeTitle'])) {
            $data['title'] = $data['welcomeTitle'];
        }

        unset($data['welcomeTitle']);

        return $data;
    }

    private function renderLevel($node)
    {
        $language = $this->getLanguage();
        $data = array();

        foreach ($node->children() as $child) {

            if ($child->ClassIdentifier != 'myvideo_menu_item') {
                continue;
            }

            // HINT skip if node is invisible ...
            if ($child->IsInvisible) {
                continue;
            }

            // HINT skip if disabled per language
            if (!$this->isLanguageVersionAvailable($child->ContentObject, $language)) {
                continue;
            }

            // TODO catch recursion
            $renderer = MyVideoRenderFactory::getInstance($child->ContentObject);

            $renderer->setLanguage($language);
            $tmp = $renderer->render();
            if ($child->childrenCount()) {
                $tmp['children'] = $this->renderLevel($child);
            }
            $data[] = $tmp;
        }

        return $data;
    }

    private function isLanguageVersionAvailable($contentObject, $language)
    {
        $success = true;

        $flagIsTranslated = in_array($language, $contentObject->availableLanguages());
        $flagIsAlwaysAvailable = $contentObject->isAlwaysAvailable();

        if (!$flagIsTranslated && !$flagIsAlwaysAvailable) {
            // mxdLog::write('obj is '.serialize($contentObject));
            mxdLog::write('SKIP IT - OBJECT NOT TRANSLATED ');
            return false;
        }

        $dataMap = $contentObject->fetchDataMap(false, $language);

        if (is_array($dataMap) &&
            array_key_exists('flag_disabled', $dataMap) &&
            $dataMap['flag_disabled']->Content() == 1
        ) {
            mxdLog::write('SKIP IT  ' . __METHOD__);
            return false;
        }

        return $success;
    }

    /**
     * use remote id as identifier part for redis key generation
     * @return string
     */
    protected function fetchKeyPartIdentifier()
    {
        return self::KEY_PREFIX . $this->ContentObject->attribute('remote_id');
    }
}
