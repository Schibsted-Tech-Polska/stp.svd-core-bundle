<?php

namespace Svd\CoreBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Menu
 */
class BaseBuilder
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var ContainerInterface */
    protected $container;

    /**
     * Construct
     *
     * @param FactoryInterface   $factory   factory
     * @param ContainerInterface $container container
     */
    public function __construct(FactoryInterface $factory, ContainerInterface $container)
    {
        $this->factory = $factory;
        $this->container = $container;
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string         $route         The name of the route
     * @param mixed          $parameters    An array of parameters
     * @param Boolean|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Get lang data
     *
     * @param ItemInterface $menu        menu
     * @param Request       $request     request
     * @param string        $classPrefix class prefix
     * @param bool          $setCurrent  flag if set current
     *
     * @return ItemInterface
     */
    protected function getLangData(ItemInterface $menu, Request $request, $classPrefix = '', $setCurrent = false)
    {
        $requestLocale = $request->get('_locale');

        $locales = $this->container->getParameter('locales');

        /** @Ignore */
        foreach ($locales as $locale) {
            /** @Ignore */
            $languageName = $this->container->get('translator')->trans('locale.' . $locale);

            $menu->addChild($locale, array(
                /** @Ignore */
                'label' => $locale,
            ));

            $menu[$locale]->setUri($this->generateUrl('homepage', array(
                '_locale' => $locale,
            )));
            $menu[$locale]->setAttributes(array('class' => $classPrefix . $locale));
            $menu[$locale]->setLinkAttributes(array('class' => $locale, 'title' => $languageName));

            // set current element
            if (($setCurrent === true) && ($requestLocale === $locale)) {
                $menu[$locale]->setCurrent(true);
            }
        }

        return $menu;
    }
}
