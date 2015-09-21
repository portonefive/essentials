<?php namespace PortOneFive\Essentials\Html;

use Illuminate\Html\HtmlBuilder as BaseHtmlBuilder;

class HtmlBuilder extends BaseHtmlBuilder
{

    protected $breadcrumbs     = [];
    protected $enqueuedScripts = [];

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

}