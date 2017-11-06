<?php

namespace Bot\CrawlerParser;

class CurlCrawler
{
    const PAGE_TIME = 'pageTime';
    const PAGE_BODY = 'pageBody';

    public function __construct()
    {
        if (!function_exists('curl_init')) {
            throw new \RuntimeException('cURL is required to use the cURL crawler.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getContent($url)
    {
        if (false === $curl = curl_init($url)) {
            throw new \RuntimeException('Unable to create a cURL handle.');
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        $response = curl_exec($curl);

        if (false === $response) {
            $error = curl_error($curl);
            curl_close($curl);

            throw new \RuntimeException(sprintf('An error occurred: %s.', $error));
        }

        $headersSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $headersSize);

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $time = curl_getinfo($curl, CURLINFO_TOTAL_TIME);

        curl_close($curl);

        if (400 == $statusCode) {
            $data = json_decode($body, true);
            $error = $data['error'];

            throw new \RuntimeException($error);
        }

        if (200 != $statusCode) {
            throw new \RuntimeException(sprintf(
                'The web service failed for an unknown reason (HTTP %s).',
                $statusCode
            ));
        }

        return [
            self::PAGE_TIME => $time,
            self::PAGE_BODY => $body
        ];
    }
}
