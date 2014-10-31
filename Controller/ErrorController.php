<?php

namespace Svd\CoreBundle\Controller;

use InvalidArgumentException;
use Svd\CoreBundle\Seo\SeoPage;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig_Environment;

//use Symfony\Component\Debug\Exception\FlattenException;

/**
 * Controller /as a service/
 */
class ErrorController extends BaseController
{
    /** @var Twig_Environment */
    protected $twig;

    /** @var bool */
    protected $debug;

    /** @var array */
    protected $numbers;

    /** @var SeoPage */
    protected $seo;

    /**
     * Constructor
     *
     * @param Twig_Environment $twig    twig environment
     * @param bool             $debug   debug flag
     * @param array            $numbers error numbers
     * @param SeoPage          $seo     seo page
     */
    public function __construct(Twig_Environment $twig, $debug, array $numbers, SeoPage $seo)
    {
        parent::__construct($twig, $debug);

        $this->numbers = $numbers;
        $this->seo = $seo;
    }

    /**
     * Show error page
     *
     * @param Request              $request   request
     * @param FlattenException     $exception a lattenException instance
     * @param DebugLoggerInterface $logger    a DebugLoggerInterface instance
     * @param string               $format    the format to use for rendering (html, xml, ...)
     *
     * @return Response
     *
     * @throws InvalidArgumentException When the exception template does not exist
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null,
        $format = 'html')
    {
        $statusCode = $exception->getStatusCode();
        $statusText = isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : '';

        if (in_array($statusCode, $this->numbers)) {
            $text = 'Error: ' . $statusCode . ' ' . $statusText;
            $this->seo->setSeo($text, $text, $text);

            $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));

            return new Response($this->twig->render(
                new TemplateReference('SvdCoreBundle', 'Error', $statusCode, $format, 'twig'),
                array(
                    'status_code'    => $statusCode,
                    'status_text'    => isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : '',
                    'exception'      => $exception,
                    'logger'         => $logger,
                    'currentContent' => $currentContent,
                )
            ));
        } else {
            return parent::showAction($request, $exception, $logger, $format);
        }
    }
}
