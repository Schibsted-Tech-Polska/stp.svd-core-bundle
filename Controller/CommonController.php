<?php

namespace Svd\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller
 */
class CommonController extends Controller
{
    /**
     * Show common footer
     *
     * @return Response
     */
    public function footerAction()
    {
        return $this->render('SvdCoreBundle:Common:_footer.html.twig', array());
    }
}
