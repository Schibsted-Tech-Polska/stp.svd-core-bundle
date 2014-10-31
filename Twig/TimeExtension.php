<?php

namespace Svd\CoreBundle\Twig;

use DateTime;
use JMS\TranslationBundle\Annotation\Ignore;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig_Extension;
use Twig_SimpleFilter;

/**
 * Extension for time filters
 */
class TimeExtension extends Twig_Extension
{
    /**
     * Container object
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructs extension object and save container object inside
     *
     * @param ContainerInterface $container container object
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns an array of filters
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('shortAgo', array($this, 'shortAgoFilter')),
            new Twig_SimpleFilter('longAgo', array($this, 'longAgoFilter'))
        );
    }

    /**
     * Returns long form of time period between defined and current time
     *
     * @param DateTime $time time object
     *
     * @return string
     */
    public function longAgoFilter($time)
    {
        return $this->agoCore($time);
    }

    /**
     * Returns short form of time period between defined and current time
     *
     * @param DateTime $time time object
     *
     * @return string
     */
    public function shortAgoFilter($time)
    {
        return $this->agoCore($time, true);
    }

    /**
     * Returns long or short form of time period between defined and current time
     *
     * @param DateTime $time    time object
     * @param bool     $isShort if short form of time period should be used
     *
     * @return string
     */
    protected function agoCore($time, $isShort = false)
    {
        $translator = $this->container->get('translator');

        if (!($time instanceof DateTime)) {
            return $isShort ? '-' : $translator->trans('admin.time_long_unknown');
        }

        $now = new DateTime();
        $diff = $time->diff($now);

        if ($diff->y > 0) {
            $name = 'years';
            $value = $diff->y;
        } elseif ($diff->m > 0) {
            $name = 'months';
            $value = $diff->m;
        } elseif ($diff->d > 0) {
            $name = 'days';
            $value = $diff->d;
        } else {
            $name = 'hours';
            $value = $diff->h;
        }

        if ($isShort) {
            /** @Ignore */
            $ret = $translator->trans('admin.time_short_' . $name, array('%count%' => $value));
        } else {
            /** @Ignore */
            $ret = $translator->transChoice('admin.time_long_' . $name, $value, array('%count%' => $value));
        }

        return $ret;
    }

    /**
     * Returns extension name
     *
     * @return string
     */
    public function getName()
    {
        return 'time_extension';
    }
}
