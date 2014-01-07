<?php
/**
 * Validates data for the highlightpage
 *
 * User: patrik Jacoby(patrik.jacoby@magicinternet.de)
 * Date: 09.12.13
 */


class MyVideoHighlightPageValidator extends MyVideoAbstractValidator
{
    const NEEDED_ITEM_NUMBER = 12;

    const ATTR_HIGHLIGHT_ITEMS = 'highlight_items';
    const ATTR_SUSHIBAR = 'sushibar';
    const ATTR_URL = "url";
    const ATTR_RSS_TITLE = "rss_title";
    const ATTR_RSS_NAME = "rss_name";

    const ERROR_RSS_NOT_COMPLATE = "Bitte füllen Sie beide Angaben für das RSS Link oder lassen Sie beide weg";
    const ERROR_NOT_VALID_URL = "Ihre gewählte URL ist nicht gültig oder entspricht nicht den vorhaben. Es müssen mindestens eine Ebene gewählt werden (/Serien) und darf maximal zwei Ebenen enthalten (/Serien/Highlights)";
    const ERROR_NO_HIGHLIGHTS = 'Es sind nicht genügend Einträge definiert. Es werden mindestens 11 Einträge erwartet, bitte fügen Sie entsprechend Videos oder Teaser hinzu.';
    const ERROR_NO_SUSHIBAR_ENTRIES = 'Es liegt keine gefüllte Sushibar vor.';

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $currentAttribute
     * @return MyVideoCustomValidationResult|void
     */
    public function validateAttribute($allAttributes, $currentAttribute)
    {
        $attributeName = $currentAttribute->contentClassAttributeIdentifier();

        switch ($attributeName) {
            case self::ATTR_URL:
                return $this->validateUrl($currentAttribute);
            case self::ATTR_RSS_TITLE:
                return $this->validateRss($allAttributes);
            case self::ATTR_HIGHLIGHT_ITEMS:
                return $this->validateHighlights($currentAttribute);
            case self::ATTR_SUSHIBAR:
                return $this->validateSushibar($currentAttribute);
        }

        return null;

    }

    /**
     * Validate url part
     * @param eZContentObjectAttribute $attribute
     * @return MyVideoCustomValidationResult
     */
    private function validateUrl($attribute)
    {
        $result = new MyVideoCustomValidationResult();
        $url = $attribute->content();

        $regexp = "/^\/([a-zA-Z0-9\-\_]+)($|(\/[a-zA-Z0-9\-\_]+)$)/";

        if (preg_match($regexp, $url) === 0) {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(self::ERROR_NOT_VALID_URL);
        }

        return $result;
    }

    /**
     * @param $allAttributes
     */
    private function validateRss($allAttributes)
    {
        $result = new MyVideoCustomValidationResult();
        $rssName = $this->getAttribute($allAttributes, self::ATTR_RSS_NAME)->content();
        $rssTitle = $this->getAttribute($allAttributes, self::ATTR_RSS_TITLE)->content();


        //both values can be empty
        if (empty($rssTitle) == false || empty($rssName) == false) {
            if (empty($rssTitle) == true || empty($rssName) == true) {
                $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
                $result->addFact(self::ERROR_RSS_NOT_COMPLATE);
            }
        }

        return $result;
    }

    /**
     * Validates that the item list has at least 11 items, as this is the minimum for the layout to work.
     * @param eZContentObjectAttribute $attribute
     * @return MyVideoCustomValidationResult
     */
    private function validateHighlights($attribute)
    {
        $result = new MyVideoCustomValidationResult();
        $data = $attribute->content();

        $highlightRelations = isset($data['relation_list']) ? $data['relation_list'] : array();
        if (count($highlightRelations) < self::NEEDED_ITEM_NUMBER) {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(self::ERROR_NO_HIGHLIGHTS);
        }

        return $result;
    }

    /**
     * Validates the "seo-sushibar" - by now only for existing items
     *
     * @param eZContentObjectAttribute $attribute
     * @return MyVideoCustomValidationResult
     */
    private function validateSushibar($attribute)
    {
        $result = new MyVideoCustomValidationResult();
        $data = $attribute->content();

        if (!$data) {
            $result->setStatus(MyVideoCustomValidationResult::STATUS_ERROR);
            $result->addFact(self::ERROR_NO_SUSHIBAR_ENTRIES);
        }

        return $result;
    }

}