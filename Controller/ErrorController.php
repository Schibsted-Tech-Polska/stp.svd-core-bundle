<?php

namespace Svd\CoreBundle\Controller;

use InvalidArgumentException;
use Svd\CoreBundle\Seo\SeoPage;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Twig_Environment;

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

    /** @var array */
    protected $errorPages;

    /**
     * Constructor
     *
     * @param Twig_Environment $twig       twig environment
     * @param bool             $debug      debug flag
     * @param array            $numbers    error numbers
     * @param SeoPage          $seo        seo page
     * @param array            $errorPages error pages
     */
    public function __construct(Twig_Environment $twig, $debug, array $numbers, SeoPage $seo, array $errorPages)
    {
        parent::__construct($twig, $debug);

        $this->numbers = $numbers;
        $this->seo = $seo;
        $this->errorPages = $errorPages;
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

            $bundle = 'SvdCoreBundle';
            $controller = 'Error';
            $name = $statusCode;
            $context = array(
                'currentContent' => $currentContent,
                'exception' => $exception,
                'logger' => $logger,
                'status_code' => $statusCode,
                'status_text' => isset(Response::$statusTexts[$statusCode]) ? Response::$statusTexts[$statusCode] : '',
            );

            foreach ($this->errorPages as $errorPage) {
                if (preg_match('#' . $errorPage['path'] . '#', $request->getPathInfo()) &&
                    in_array($format, $errorPage['formats'])
                ) {
                    $bundle = $errorPage['bundle'];
                    $controller = $errorPage['controller'];
                    $name = str_replace('%code%', $statusCode, $errorPage['name']);
                    foreach ($errorPage['view_vars'] as $key => $value) {
                        $context[$key] = $value;
                    }
                    break;
                }
            }

            $templateReference = new TemplateReference($bundle, $controller, $name, $format, 'twig');

            return new Response($this->twig->render($templateReference, $context));
        } else {
            return parent::showAction($request, $exception, $logger, $format);
        }
    }
}
