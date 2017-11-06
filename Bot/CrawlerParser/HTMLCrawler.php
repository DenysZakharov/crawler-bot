<?php

namespace Bot\CrawlerParser;

class HTMLCrawler
{
    /**
     * @var \DOMNode
     */
    private $dom;

    /**
     * HTMLCrawler constructor.
     */
    public function __construct()
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
    }

    /**
     * @param \ArrayIterator $urlCollection
     * @param string         $url
     *
     * @return \ArrayIterator
     */
    public function replenishURLCollection(\ArrayIterator $urlCollection, string $url): \ArrayIterator
    {
        $anchors = $this->dom->getElementsByTagName('a');
        $arrayCopy = $urlCollection->getArrayCopy();
        foreach ($anchors as $element) {
            $href = $element->getAttribute('href');
            // TODO Learn to work with different urls & fix bad way to check same url
            if ($this->isCorrectHref($href, $url) && $this->checkToSameUrl($href, $arrayCopy)) {
                $urlCollection->append(
                    [
                        'url' => $href,
                        'used' => false,
                        'time' => null,
                        'tagCount' => 0
                    ]
                );
            }
        }

        return $urlCollection;
    }

    /**
     * @param $html
     */
    public function loadHtml($html)
    {
        $internalErrors = libxml_use_internal_errors(true);
        $this->dom->loadHTML($html);
        libxml_use_internal_errors($internalErrors);
    }


    /**
     * @param $tag
     *
     * @return int
     */
    public function getTagCount(string $tag): int
    {
        $tags = $this->dom->getElementsByTagName($tag);

        return $tags->length;
    }

    /**
     * @param string $href
     * @param string $url
     *
     * @return bool
     */
    private function isCorrectHref(string $href, string $url): bool
    {
        return !empty($href) && (strpos($href, $url) !== false) && $href !== '/#' && $href !== '/';
    }

    /**
     * @param string $url
     * @param array  $arrayCopy
     *
     * @return bool
     */
    private function checkToSameUrl(string $url, array $arrayCopy): bool
    {
        foreach ($arrayCopy as $key => $value) {
            if ($url === $value['url']) {
                return false;
            }
        }

        return true;
    }
}
