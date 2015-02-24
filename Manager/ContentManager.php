<?php

namespace Svd\CoreBundle\Manager;

use RuntimeException;

/**
 * Manager
 */
class ContentManager
{
    /**
     * Fetch response /extended/
     *
     * @param string $url     url
     * @param array  $options options
     *
     * @return array
     */
    public function fetchResponseExtended($url, array $options = array())
    {
        $options = $this->setOptions($options);
        $response = array();

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $response['content'] = curl_exec($ch);
        $response['effectiveUrl'] = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $response['errorMessage'] = curl_error($ch);
        $response['errorNumber'] = curl_errno($ch);
        $response['httpCode'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $response['redirectCount'] = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
        curl_close($ch);

        return $response;
    }

    /**
     * Fetch response /simple/
     *
     * @param string $url     url
     * @param array  $options options
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function fetchResponse($url, array $options = array())
    {
        $response = $this->fetchResponseExtended($url, $options);

        if (($response['httpCode'] == 200) && ($response['errorNumber'] == 0)) {
            return $response['content'];
        } else {
            throw new RuntimeException(sprintf("Can't fetch response from url: %s, httpCode: %d, errorNumber: %d",
                $url, $response['httpCode'], $response['errorNumber']));
        }
    }

    /**
     * Set options
     *
     * @param array $options options
     *
     * @return array
     */
    protected function setOptions(array $options = array())
    {
        // @README: marge array preserving keys
        $options = $options + array(
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 30,
        );

        return $options;
    }
}
