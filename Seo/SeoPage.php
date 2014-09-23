<?php

namespace Svd\CoreBundle\Seo;

use Sonata\SeoBundle\Seo\SeoPageInterface;

/**
 * Seo
 */
class SeoPage
{
    /** @const separator: title */
    const SEPARATOR_TITLE = ' - ';

    /** @const separator: description */
    const SEPARATOR_DESCRIPTION = '. ';

    /** @const separator: keywords */
    const SEPARATOR_KEYWORDS = ', ';

    /** @var SeoPageInterface */
    protected $seoPage;

    /**
     * Constructor
     *
     * @param SeoPageInterface $seoPage seoPage
     */
    public function __construct(SeoPageInterface $seoPage)
    {
        $this->seoPage = $seoPage;
    }

    /**
     * Set seo
     *
     * @param mixed $title       title
     * @param mixed $description description
     * @param mixed $keywords    keywords
     *
     * @return SeoPage
     */
    public function setSeo($title, $description, $keywords)
    {
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setKeywords($keywords);

        return $this;
    }

    /**
     * Set title
     *
     * @param string|array $data      data
     * @param string       $separator separator
     * @param bool         $prepend   prepend flag
     *
     * @return SeoPage
     */
    public function setTitle($data = '', $separator = self::SEPARATOR_TITLE, $prepend = true)
    {
        if (!is_array($data)) {
            $data = array($data);
        }

        // add current text
        $title = $this->seoPage->getTitle();
        $data[] = $title;

        // create array, but only with valid values
        $data = array_filter($data, function ($item) {
            $item = trim($item);

            return (!empty($item));
        });

        // perhaps change an order
        if (!$prepend) {
            $data = array_reverse($data);
        }

        // add separators
        $text = implode($separator, $data);


        $this->seoPage->setTitle($text);

        return $this;
    }

    /**
     * Set description
     *
     * @param string|array $data      data
     * @param string       $separator separator
     * @param bool         $prepend   prepend flag
     *
     * @return SeoPage
     */
    public function setDescription($data = '', $separator = self::SEPARATOR_DESCRIPTION, $prepend = true)
    {
        if (!is_array($data)) {
            $data = array($data);
        }

        // add current text
        $metas = $this->seoPage->getMetas();
        $data[] = $metas['name']['description'][0];

        // create array, but only with valid values
        $data = array_filter($data, function ($item) {
            $item = trim($item);

            return (!empty($item));
        });

        // perhaps change an order
        if (!$prepend) {
            $data = array_reverse($data);
        }

        // add separators
        $text = implode($separator, $data);


        $this->seoPage->addMeta('name', 'description', $text);

        return $this;
    }

    /**
     * Set keywords
     *
     * @param string|array $data      data
     * @param string       $separator separator
     * @param bool         $prepend   prepend flag
     *
     * @return SeoPage
     */
    public function setKeywords($data = '', $separator = self::SEPARATOR_KEYWORDS, $prepend = true)
    {
        if (!is_array($data)) {
            $data = array($data);
        }

        // add current text
        $metas = $this->seoPage->getMetas();
        $data[] = $metas['name']['keywords'][0];

        // create array, but only with valid values
        $data = array_filter($data, function ($item) {
            $item = trim($item);

            return (!empty($item));
        });

        // convert to lowercase
        $data = array_map(function ($item) {
            $item = mb_strtolower($item, 'UTF-8');

            return $item;
        }, $data);

        // perhaps change an order
        if (!$prepend) {
            $data = array_reverse($data);
        }

        // add separators
        $text = implode($separator, $data);


        $this->seoPage->addMeta('name', 'keywords', $text);

        return $this;
    }

    /**
     * Prepend title
     *
     * @param string|array $data      data
     * @param string       $separator separator
     *
     * @return SeoPage
     */
    public function prependTitle($data = '', $separator = self::SEPARATOR_TITLE)
    {
        $this->setTitle($data, $separator, true);

        return $this;
    }

    /**
     * Append title
     *
     * @param string|array $data      data
     * @param string       $separator separator
     *
     * @return SeoPage
     */
    public function appendTitle($data = '', $separator = self::SEPARATOR_TITLE)
    {
        $this->setTitle($data, $separator, false);

        return $this;
    }

    /**
     * Prepend description
     *
     * @param string|array $data      data
     * @param string       $separator separator
     *
     * @return SeoPage
     */
    public function prependDescription($data = '', $separator = self::SEPARATOR_DESCRIPTION)
    {
        $this->setDescription(true, $data, $separator);

        return $this;
    }

    /**
     * Append description
     *
     * @param string|array $data      data
     * @param string       $separator separator
     *
     * @return SeoPage
     */
    public function appendDescription($data = '', $separator = self::SEPARATOR_DESCRIPTION)
    {
        $this->setDescription(false, $data, $separator);

        return $this;
    }

    /**
     * Prepend keywords
     *
     * @param string|array $data      data
     * @param string       $separator separator
     *
     * @return SeoPage
     */
    public function prependKeywords($data = '', $separator = self::SEPARATOR_KEYWORDS)
    {
        $this->setKeywords(true, $data, $separator);

        return $this;
    }

    /**
     * Append keywords
     *
     * @param string|array $data      data
     * @param string       $separator separator
     *
     * @return SeoPage
     */
    public function appendKeywords($data = '', $separator = self::SEPARATOR_KEYWORDS)
    {
        $this->setKeywords(false, $data, $separator);

        return $this;
    }
}
