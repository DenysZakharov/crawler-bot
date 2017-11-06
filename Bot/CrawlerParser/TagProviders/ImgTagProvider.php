<?php

namespace Bot\CrawlerParser\TagProviders;

class ImgTagProvider implements TagProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getTag(): string
    {
        return 'img';
    }
}
