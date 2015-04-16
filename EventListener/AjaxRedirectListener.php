<?php

namespace Svd\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Event listener
 */
class AjaxRedirectListener
{
    /** @var array|null */
    protected $selectedRoutes;

    /** @var string */
    protected $content;

    /** @var integer */
    protected $statusCode;

    /**
     * Constructor
     *
     * @param array|null $selectedRoutes selected routes
     * @param string     $content        content
     * @param integer    $statusCode     status code
     */
    public function __construct(array $selectedRoutes = null, $content = '', $statusCode = Response::HTTP_OK)
    {
        $this->selectedRoutes = $selectedRoutes;
        $this->content = $content;
        $this->statusCode = $statusCode;
    }

    /**
     * On kernel response
     *
     * @param FilterResponseEvent $event event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->isXmlHttpRequest() &&
            (empty($this->selectedRoutes) || in_array($request->get('_route'), $this->selectedRoutes))) {
            $location = $response->headers->get('Location');
            if (!empty($location)) {
                if (isset($this->statusCode)) {
                    $response->setStatusCode($this->statusCode);
                }
                if (isset($this->content)) {
                    $response->setContent($this->content);
                }
                $response->headers->add(array(
                    'Access-Control-Expose-Headers' => $this->mergeAccessControlExposeHeaders($response, array(
                        'X-Location'
                    )),
                    'X-Location' => $response->headers->get('Location'),
                ));
                $response->headers->remove('Location');
            }
        }
    }

    /**
     * Merge access control expose headers
     *
     * @param Response $response   response
     * @param array    $newHeaders new headers
     *
     * @return array
     */
    protected function mergeAccessControlExposeHeaders(Response $response, array $newHeaders = array())
    {
        $headersString = $response->headers->get('Access-Control-Expose-Headers');
        if (!empty($headersString)) {
            $headers = explode(',', str_replace(' ', '', $headersString));
        } else {
            $headers = array();
        }

        foreach ($newHeaders as $newHeader) {
            if (!in_array($newHeader, $headers)) {
                array_push($headers, $newHeader);
            }
        }

        return $headers;
    }
}
