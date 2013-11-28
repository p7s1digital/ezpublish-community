<?php

class MyVideoTagpageValidator extends MyVideoAbstractValidator
{
    const ATTR_TITLE = 'title';
    const ATTR_URL_PART = 'url_part';
    const ATTR_KEYWORDS = 'keywords';

    /**
     * @param ezContentObjectAttribute[] $allAttributes
     * @param ezContentObjectAttribute $currentAttribute
     *
     * @return MyVideoCustomValidationResult
     */
    public function validateAttribute($allAttributes, $currentAttribute)
    {
        $attributeName = $currentAttribute->contentClassAttributeIdentifier();

        switch ($attributeName) {
            case self::ATTR_TITLE:
                return $this->validateTitle($currentAttribute);

            case self::ATTR_URL_PART:
                return $this->validateUrlPart($currentAttribute);

            case self::ATTR_KEYWORDS:
                return $this->validateKeywords($currentAttribute);

            default:
                break;
        }

        return null;
    }

    /**
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    private function validateTitle($attribute)
    {
        $title = $attribute->content();

        $tagPages = $this->getAllTagPages();

        $facts = array();

        /** @var ezContentObject $tagPage */
        foreach ($tagPages as $tagPage) {
            $otherTitleAttr = $this->helper->getAttribute($tagPage, self::ATTR_TITLE);
            $otherTitle = $otherTitleAttr ? $otherTitleAttr->content() : null;

            if ($otherTitle == $title && $attribute->attribute('id') != $otherTitleAttr->attribute('id')) {
                $facts[] =
                    'Es gibt eine weitere Tagpage mit dem Title "' . $title . '": ' .
                    'id=<a href="/content/edit/' . $tagPage->ID . '">' . $tagPage->ID . '</a>';
            }
        }

        return new MyVideoCustomValidationResult(
            count($facts) == 0 ? MyVideoCustomValidationResult::STATUS_OK : MyVideoCustomValidationResult::STATUS_ERROR,
            $facts
        );
    }

    /**
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    private function validateUrlPart($attribute)
    {
        $urlPart = $attribute->content();

        $tagPages = $this->getAllTagPages();

        $facts = array();

        /** @var ezContentObject $tagPage */
        foreach ($tagPages as $tagPage) {
            $otherUrlPartAttr = $this->helper->getAttribute($tagPage, self::ATTR_URL_PART);
            $otherUrlPart = $otherUrlPartAttr ? $otherUrlPartAttr->content() : null;

            if ($otherUrlPart == $urlPart && $attribute->attribute('id') != $otherUrlPartAttr->attribute('id')) {
                $facts[] =
                    'Es gibt eine weitere Tagpage mit dem URL-identifier "' . $urlPart . '": ' .
                    'id=<a href="/content/edit/' . $tagPage->ID . '">' . $tagPage->ID . '</a>';
            }
        }

        return new MyVideoCustomValidationResult(
            count($facts) == 0 ? MyVideoCustomValidationResult::STATUS_OK : MyVideoCustomValidationResult::STATUS_ERROR,
            $facts
        );
    }

    /**
     * @param ezContentObjectAttribute $attribute
     *
     * @return MyVideoCustomValidationResult
     */
    private function validateKeywords($attribute)
    {
        $keywords = $this->helper->getCommaSeparatedValues($attribute);

        $tagPages = $this->getAllTagPages();

        $facts = array();

        /** @var ezContentObject $tagPage */
        foreach ($tagPages as $tagPage) {
            $otherKeywordsAttr = $this->helper->getAttribute($tagPage, self::ATTR_KEYWORDS);
            $otherKeywords = $otherKeywordsAttr ? $this->helper->getCommaSeparatedValues($otherKeywordsAttr) : array();

            if ($attribute->attribute('id') != $otherKeywordsAttr->attribute('id')) {
                $foundInOther = array();

                foreach ($keywords as $keyword) {
                    if (in_array($keyword, $otherKeywords)) {
                        $foundInOther[] = $keyword;
                    }
                }

                if (count($foundInOther) > 0) {
                    $facts[] =
                        'Es gibt eine weitere Tagpage mit den Keywords "' . implode('", "', $foundInOther) . '": ' .
                        'id=<a href="/content/edit/' . $tagPage->ID . '">' . $tagPage->ID . '</a>';
                }
            }
        }

        return new MyVideoCustomValidationResult(
            count($facts) == 0 ? MyVideoCustomValidationResult::STATUS_OK : MyVideoCustomValidationResult::STATUS_WARNING,
            $facts
        );
    }

    private function getAllTagPages()
    {
        return $tagPages = ezContentObject::fetchList(
            true,
            array('contentclass_id' => $this->getTagPageContentClassId())
        );

    }

    /**
     * @return int
     */
    private function getTagPageContentClassId()
    {
        return ezContentClass::classIDByIdentifier(MyVideoContentClassIds::TAG_PAGE_IDENTIFIER);
    }
}
