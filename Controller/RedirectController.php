<?php

namespace Svd\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller
 *
 * README:
 * Before this commit, the current locale was stored in the session (if one
 * was already started). That way, for the next requests, even if the
 *
 * But this is a really bad practice as it means that the same URL can have
 * a different content depending on the previous requests. It would have
 * been better if the Vary header was set but the locale can be different
 * from the value coming from the Accept-Language anyway.
 */
class RedirectController extends Controller
{
    /**
     * Redirect to admin page in preferred language version
     *
     * @param Request $request request
     *
     * @return RedirectResponse
     */
    public function adminAction(Request $request)
    {
        $adminIndex = $this->container->getParameter('svd_core.urls.admin_index');
        $locale = $request->getPreferredLanguage($this->container->getParameter('svd_core.locales'));

        return $this->redirect($this->generateUrl($adminIndex, array(
            '_locale' => $locale,
        )));
    }

    /**
     * Redirect to page in preferred language version
     *
     * @param Request $request request
     *
     * @return RedirectResponse
     */
    public function defaultAction(Request $request)
    {
        $homepage = $this->container->getParameter('svd_core.urls.homepage');
        $locale = $request->getPreferredLanguage($this->container->getParameter('svd_core.locales'));

        return $this->redirect($this->generateUrl($homepage, array(
            '_locale' => $locale,
        )));
    }
}
