<?php namespace PortOneFive\Essentials\Html;

use Collective\Html\HtmlBuilder as BaseHtmlBuilder;
use Route;

class HtmlBuilder extends BaseHtmlBuilder
{

    protected $breadcrumbs     = [];
    protected $enqueuedScripts = [];
    protected $pageTitleVars   = [];
    protected $pageTitle;

    public function breadcrumb($label = null, $link = null)
    {
        if (is_null($label)) {

            $breadcrumbs = $this->breadcrumbs;

            $this->breadcrumbs = [];

            return view('partial.breadcrumbs', ['breadcrumbs' => $breadcrumbs]);
        }

        $this->breadcrumbs[] = ['label' => $label, 'link' => $link];
    }

    public function enqueueScript($src)
    {
        $this->enqueuedScripts[] = $src;
    }

    public function getEnqueuedScripts()
    {
        return $this->enqueuedScripts;
    }

    public function enqueueStyle($src)
    {
        $this->enqueuedScripts[] = $src;
    }

    public function getEnqueuedStyles()
    {
        return $this->enqueuedScripts;
    }

    public function setPageTitleVariable($key, $value)
    {
        $this->pageTitleVars[$key] = $value;
    }

    public function getPageTitle()
    {
        return isset($this->pageTitle) ? $this->pageTitle : trans(
            'page_titles.' . Route::currentRouteName(),
            $this->pageTitleVars
        );
    }

    public function setPageTitle($title)
    {
        $this->pageTitle = $title;
    }
}