<?php namespace PortOneFive\Essentials\Html;

use Illuminate\Html\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{

    public function delete($title, $options = [])
    {
        if (isset($options['class'])) {
            $options['class'] .= ' OverlayTrigger';
        } else {
            $options['class'] = 'OverlayTrigger';
        }

        $options['name']  = '_method';
        $options['value'] = 'delete';

        return '<button id="delete" type="submit" ' . $this->html->attributes($options) . '>' . $title . '</button>';
    }

    public function toggle($name, $value = null, $checked = null, array $options = [])
    {
        if ($this->getCheckedState('checkbox', $name, $value, $checked)) {
            $options['checked'] = 'checked';
        }

        return '<div data-switch>'
               . $this->input('checkbox', $name, 1, $options + ['id' => $name]) . $this->label($name, $value, $options)
               . '</div>';
    }

}