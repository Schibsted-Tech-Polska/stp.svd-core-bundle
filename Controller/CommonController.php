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
        $currentYear = date('Y');
        $creationYear = $this->container->getParameter('svd_core.parameters.creation_year');

        $date = ($currentYear > $creationYear ? $creationYear . ' - ' . $currentYear : $creationYear);
        $companyName = $this->container->getParameter('svd_core.parameters.company_name');

        return $this->render('SvdCoreBundle:Common:_footer.html.twig', array(
            'date' => $date,
            'companyName' => $companyName,
        ));
    }
}
