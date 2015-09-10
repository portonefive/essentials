<?php namespace PortOneFive\Essentials\Facades;

use Illuminate\Html\HtmlFacade;
use Route;

class HTML extends HtmlFacade
{

    protected static $pageTitle;
    protected static $pageTitleVars   = [];
    protected static $enqueuedScripts = [];
    protected static $enqueuedStyles  = [];
    protected static $metaData        = [];
    public static $breadcrumbs     = [];

    public static function enqueueScript($src)
    {
        self::$enqueuedScripts[] = $src;
    }

    public static function enqueueStyle($src)
    {
        self::$enqueuedStyles[] = $src;
    }

    public static function getEnqueuedScripts()
    {
        return self::$enqueuedScripts;
    }

    public static function getEnqueuedStyles()
    {
        return self::$enqueuedStyles;
    }

    public static function setPageTitleVariable($key, $value)
    {
        self::$pageTitleVars[$key] = $value;
    }

    public static function getPageTitle()
    {
        return isset(self::$pageTitle) ? self::$pageTitle : trans(
            'page_titles.' . Route::currentRouteName(),
            self::$pageTitleVars
        );
    }

    public static function setPageTitle($title)
    {
        self::$pageTitle = $title;
    }

    public static function addMetaData($name, $content)
    {
        self::$metaData[$name] = $content;
    }

    public static function getMetaData()
    {
        return self::$metaData;
    }

    public static function addBreadcrumb($label, $link = null)
    {
        self::$breadcrumbs[] = [
            'label' => $label,
            'link'  => $link
        ];
    }

    public static function renderBreadcrumbs()
    {
        return view('partial.breadcrumbs', ['breadcrumbs' => self::$breadcrumbs]);
    }

    public static function checkboxUnit($id, $title, $classes = '')
    {
        $return = '<dl class="checkbox-unit ' . $classes . '">';

        $return .= '<dt>' . Form::checkbox($id, 1, null, ['id' => $id]) . '</dt>';
        $return .= '<dd>' . Form::label($id, $title) . '</dd>';

        $return .= '</dl>';

        return $return;
    }


    public static function radioUnit($id, $title, $classes = '')
    {
        $return = '<dl class="radio-unit ' . $classes . '">';

        $return .= '<dt>' . Form::label($id, $title) . '</dt>';
        $return .= '<dd>'
                   . Form::radio($id, 1, ['id' => $id])
                   . ' Y '
                   . Form::radio($id, 0, ['id' => $id . '_no'])
                   . ' N</dd>';

        $return .= '</dl>';

        return $return;
    }
}
