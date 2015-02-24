<?php

namespace Svd\CoreBundle\Util;

use Doctrine\Common\Util\Debug;
use finfo;
use Svd\CoreBundle\Manager\ContentManager;
use Svd\CoreBundle\MimeType\MimeTypeMatcher;

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
     * Get content type
     *
     * @param string  $url                URL
     * @param string  $defaultContentType default content type
     * @param boolean $checkRealType      check real type
     *
     * @return string
     */
    public static function getContentType($url, $defaultContentType = '', $checkRealType = false)
    {
        if ($checkRealType) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $type = $finfo->buffer(file_get_contents($url));

            if (empty($type)) {
                $type = $defaultContentType;
            }
        } else {
            $mimeTypes = array_flip((new MimeTypeMatcher())->getMatches());
            $mimeTypes['jpg'] = 'image/jpeg';

            $urlParts = explode('?', $url);
            $url = $urlParts[0];

            $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
            $type = array_key_exists($extension, $mimeTypes) ? $mimeTypes[$extension] : $defaultContentType;
        }

        return $type;
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
        $manager = new ContentManager();

        $response = $manager->fetchResponseExtended($url, $options);

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
        $manager = new ContentManager();

        $response = $manager->fetchResponse($url, $options);

        return $response;
    }

    /**
     * Leave URL parts
     *
     * @param string $url          URL
     * @param int    $partsToLeave parts to leave
     * @param array  $params       params
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function leaveUrlParts($url, $partsToLeave, array $params = array())
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
            if (isset($params['cutFromUrlPath']) && !empty($params['cutFromUrlPath'])) {
                $path = preg_replace($params['cutFromUrlPath'], '', $parsedUrl['path']);
            } else {
                $path = $parsedUrl['path'];
            }
        } else {
            $path = '';
        }
        if (($partsToLeave & self::URL_QUERY) && isset($parsedUrl['query'])) {
            if (isset($params['allowedUrlParamNames']) && is_array($params['allowedUrlParamNames'])) {
                parse_str($parsedUrl['query'], $queryParams);
                foreach ($queryParams as $key => $value) {
                    if (!in_array($key, $params['allowedUrlParamNames'])) {
                        unset($queryParams[$key]);
                    }
                }
                ksort($queryParams);
                $parsedUrl['query'] = http_build_query($queryParams);
            }
            $query = !empty($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
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
     * @param array  $params        params
     *
     * @return string
     */
    public static function removeUrlParts($url, $partsToRemove, array $params = array())
    {
        $ret = self::leaveUrlParts($url, ~$partsToRemove, $params);

        return $ret;
    }

    /**
     * Get url parts ids
     *
     * @return array
     */
    public static function getUrlPartsIds()
    {
        $urlParts = [
            self::URL_SCHEME,
            self::URL_HOST,
            self::URL_PORT,
            self::URL_USER,
            self::URL_PASS,
            self::URL_PATH,
            self::URL_QUERY,
            self::URL_FRAGMENT,
        ];

        return $urlParts;
    }

    /**
     * Get url parts labels
     *
     * @return array
     */
    public static function getUrlPartsLabels()
    {
        $urlParts = [
            self::URL_SCHEME   => 'URL_PART_SCHEME',
            self::URL_HOST     => 'URL_PART_HOST',
            self::URL_PORT     => 'URL_PART_PORT',
            self::URL_USER     => 'URL_PART_USER',
            self::URL_PASS     => 'URL_PART_PASSWORD',
            self::URL_PATH     => 'URL_PART_PATH',
            self::URL_QUERY    => 'URL_PART_QUERY',
            self::URL_FRAGMENT => 'URL_PART_FRAGMENT',
        ];

        return $urlParts;
    }
}
