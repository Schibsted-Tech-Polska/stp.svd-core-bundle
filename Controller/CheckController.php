<?php

namespace Svd\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller
 */
class CheckController extends Controller
{
    /**
     * Show ping
     *
     * @return Response
     */
    public function pingAction()
    {
        $response = new Response('ok', Response::HTTP_OK, [
            'content-type' => 'text/html',
        ]);

        return $response;
    }

    /**
     * Show pong
     *
     * @param int $age age
     *
     * @return Response
     */
    public function pongAction($age = 86400)
    {
        $response = $this->pingAction();

        $response->setPublic();
        $response->setMaxAge($age);
        $response->setSharedMaxAge($age);

        return $response;
    }
}
