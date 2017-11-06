<?php

namespace Bot\CrawlerParser\TagProviders;

interface TagProviderInterface
{
    /**
     * @return string
     */
    public function getTag(): string;
}
