<?php

namespace Svd\CoreBundle\Menu;

use Knp\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;

/**
 * Menu
 */
class MenuBuilder extends BaseBuilder
{
    /**
     * Create menu lang
     *
     * @param Request $request request
     *
     * @return MenuItem
     */
    public function createMenuLang(Request $request)
    {
        $menu = $this->factory->createItem('lang')->setChildrenAttribute('class', 'lang');

        $menu = $this->getLangData($menu, $request, 'lang-item-nav ');

        return $menu;
    }
}
