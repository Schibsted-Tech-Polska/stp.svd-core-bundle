<?php

namespace Svd\CoreBundle\Manager;

use Symfony\Component\HttpFoundation\Response;

/**
 * Manager
 */
class ContentFileManager extends ContentManager
{
    /** @var string */
    protected $path;

    /**
     * Constructor
     *
     * @param string $path path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

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
        $response = array(
            'content' => $options[CURLOPT_RETURNTRANSFER] == 1 ? '' : true,
            'effectiveUrl' => $url,
            'errorMessage' => '',
            'errorNumber' => CURLE_OK,
            'httpCode' => Response::HTTP_OK,
            'redirectCount' => 0,
        );

        $filePath = str_replace('%filename%', md5($url), $this->path);
        if (!is_file($filePath)) {
            $response['httpCode'] = Response::HTTP_NOT_FOUND;
        } elseif (!is_readable($filePath)) {
            $response['httpCode'] = Response::HTTP_UNAUTHORIZED;
        } else {
            $content = file_get_contents($filePath);
            if ($content !== false) {
                $response['content'] = $options[CURLOPT_RETURNTRANSFER] == 1 ? $content : true;
            } else {
                $response['httpCode'] = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        }

        return $response;
    }
}
