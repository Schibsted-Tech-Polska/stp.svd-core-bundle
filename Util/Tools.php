<?php

namespace Svd\CoreBundle\Util;

use Doctrine\Common\Util\Debug;

/**
 * Util
 */
class Tools
{
    /** @const int */
    const URL_SCHEME = 1;

    /** @const int */
    const URL_HOST = 2;

    /** @const int */
    const URL_PORT = 4;

    /** @const int */
    const URL_USER = 8;

    /** @const int */
    const URL_PASS = 16;

    /** @const int */
    const URL_PATH = 32;

    /** @const int */
    const URL_QUERY = 64;

    /** @const int */
    const URL_FRAGMENT = 128;

    /**
     * Prints a dump of the public, protected and private properties of var
     *
     * @param mixed   $var       variable to dump
     * @param integer $maxDepth  maximum nesting level for object properties
     * @param boolean $stripTags whether output should strip HTML tags
     */
    public static function dump($var, $maxDepth = 2, $stripTags = false)
    {
        Debug::dump($var, $maxDepth, $stripTags);
    }

    /**
     * Convert input data to safe string
     *
     * @param string $input  input data
     * @param int    $length length
     * @param array  $params params
     *
     * @return string
     */
    public static function safeString($input, $length = 128, $params = array())
    {
        $params = array_merge(array(
            'encoding' => 'UTF-8',
            'filter' => FILTER_SANITIZE_STRING,
            'options' => FILTER_FLAG_ENCODE_AMP | FILTER_FLAG_STRIP_LOW,
        ), $params);

        $return =
            mb_substr(trim(filter_var($input, $params['filter'], $params['options'])), 0, $length, $params['encoding']);

        return $return;
    }

    /**
     * Humanize time
     *
     * @param int $time time /in milliseconds/
     *
     * @return string
     */
    public static function humanizeTime($time)
    {
        $time /= 1000;

        $ms = round($time - intval($time), 3) * 1000;
        $time = floor($time);

        $min = floor($time / 60);
        $sec = floor($time - ($min * 60));

        $r = sprintf('%u:%02u.%03u min', $min, $sec, $ms);

        return $r;
    }

    /**
     * Humanize memory
     *
     * @param int $memory memory /in bytes/
     *
     * @return string
     */
    public static function humanizeMemory($memory)
    {
        $kb = $memory / 1024;
        $ret = number_format($kb, 3, '.', ' ') . ' KB';

        return $ret;
    }

    /**
     * Change document's encoding, return null in case of error
     *
     * @param string $document        document's content
     * @param string $outputEncoding  output encoding
     * @param string $defaultEncoding default encoding to use in case of document's encoding missed
     *
     * @return string|null
     */
    public static function convertEncoding($document, $outputEncoding = 'UTF-8', $defaultEncoding = 'UTF-8')
    {
        if (preg_match('/\<meta[^\>]+charset *= *["\']?([a-zA-Z\-0-9]+)/i', $document, $matches) ||
            preg_match('/\<\?xml[^\>]+encoding *= *["\']?([a-zA-Z\-0-9]+)/i', $document, $matches)
        ) {
            $encoding = $matches[1];
        } else {
            $encoding = $defaultEncoding;
        }

        if ($encoding != $outputEncoding) {
            $document = mb_convert_encoding($document, $outputEncoding, $encoding);
        }

        return $document;
    }


    /**
     * Fetch response /extended/
     *
     * @param string $url     url
     * @param array  $options options
     *
     * @return array
     */
    public static function fetchResponseExtended($url, array $options = array())
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
     * @throws \RuntimeException
     */
    public static function fetchResponse($url, array $options = array())
    {
        $response = self::fetchResponseExtended($url, $options);

        if (($response['httpCode'] == 200) && ($response['errorNumber'] == 0)) {
            return $response['content'];
        } else {
            throw new \RuntimeException(sprintf("Can't fetch response from url: %s, httpCode: %d, errorNumber: %d",
                $url, $response['httpCode'], $response['errorNumber']));
        }
    }

    /**
     * Leave URL parts
     *
     * @param string $url          URL
     * @param int    $partsToLeave parts to leave
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function leaveUrlParts($url, $partsToLeave)
    {
        $parsedUrl = parse_url($url);

        if (($partsToLeave & self::URL_SCHEME) && isset($parsedUrl['scheme'])) {
            $scheme = $parsedUrl['scheme'] . '://';
        } else {
            $scheme = '';
        }
        if (($partsToLeave & self::URL_HOST) && isset($parsedUrl['host'])) {
            $host = $parsedUrl['host'];
        } else {
            $host = '';
        }
        if (($partsToLeave & self::URL_PORT) && isset($parsedUrl['port'])) {
            $port = ':' . $parsedUrl['port'];
        } else {
            $port = '';
        }
        if (($partsToLeave & self::URL_USER) && isset($parsedUrl['user'])) {
            $user = $parsedUrl['user'];
        } else {
            $user = '';
        }
        if (($partsToLeave & self::URL_PASS) && isset($parsedUrl['pass'])) {
            $pass = ':' . $parsedUrl['pass'];
        } else {
            $pass = '';
        }
        $pass = $user || $pass ? $pass . '@' : '';
        if (($partsToLeave & self::URL_PATH) && isset($parsedUrl['path'])) {
            $path = $parsedUrl['path'];
        } else {
            $path = '';
        }
        if (($partsToLeave & self::URL_QUERY) && isset($parsedUrl['query'])) {
            $query = '?' . $parsedUrl['query'];
        } else {
            $query = '';
        }
        if (($partsToLeave & self::URL_FRAGMENT) && isset($parsedUrl['fragment'])) {
            $fragment = '#' . $parsedUrl['fragment'];
        } else {
            $fragment = '';
        }

        $ret = $scheme . $user . $pass . $host . $port . $path . $query . $fragment;

        return $ret;
    }

    /**
     * Remove URL parts
     *
     * @param string $url           URL
     * @param int    $partsToRemove parts to remove
     *
     * @return string
     */
    public static function removeUrlParts($url, $partsToRemove)
    {
        $ret = self::leaveUrlParts($url, ~$partsToRemove);

        return $ret;
    }
}
