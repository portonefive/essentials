<?php namespace PortOneFive\Essentials\Facades;

use Illuminate\Html\FormFacade;

class Form extends FormFacade
{
    public static function delete($title)
    {
        return '<button id="delete" type="submit" name="_method" value="delete" class="OverlayTrigger">' . $title . '</button>';
    }
}