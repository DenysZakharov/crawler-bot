<?php

namespace Bot;

use Bot\CrawlerParser\CurlCrawler;
use Bot\CrawlerParser\HTMLCrawler;
use Bot\ReportGenerator\ReportGenerator;

class BotGenerator
{
    // TODO change to universal command parameter for different sites
    const SITE_NAME = 'https://www.olegg.com';
                      //'https://wordpress.com/';

    /**
     * @var CurlCrawler
     */
    private $curlCrawler;

    /**
     * @var HTMLCrawler
     */
    private $HTMLCrawler;

    /**
     * @var ReportGenerator
     */
    private $reportCenerator;

    /**
     * @var \ArrayIterator
     */
    private $resultCollection;

    /**
     * @var string
     */
    private $searchingTag;

    /**
     * BotGenerator constructor.
     */
    public function __construct()
    {
        $this->curlCrawler = new CurlCrawler();
        $this->HTMLCrawler = new HTMLCrawler();
        $this->reportCenerator = new ReportGenerator();
        // TODO create custom ArrayCollection
        $this->resultCollection = new \ArrayIterator();
        $this->resultCollection->append([
            'url' => self::SITE_NAME,
            'used' => false,
            'time' => null,
            'tagCount' => 0
        ]);
        $this->searchingTag = 'img';
    }

    /**
     * @return bool
     */
    public function run(): bool
    {
        foreach ($this->resultCollection as $key => &$value) {
            if ($value['used']) {
                break;
            }

            $content = $this->curlCrawler->getContent($value['url']);
            $this->HTMLCrawler->loadHtml($content[CurlCrawler::PAGE_BODY]);
            $tagCount = $this->HTMLCrawler->getTagCount($this->searchingTag);
            $this->HTMLCrawler->replenishURLCollection($this->resultCollection, self::SITE_NAME);
            $value['time'] = $content[CurlCrawler::PAGE_TIME];
            $value['tagCount'] = $tagCount;
            $value['used'] = true;
        }

        return $this->reportCenerator->setToFile($this->resultCollection);
    }
}
