<?php

/**
 * Custom blade tag to import/enqueue javascript
 * Usage: @js('path/to/file.js')
 */
Blade::directive(
    'js',
    function ($expression) {
        return "<?php HTML::enqueueScript{$expression}; ?>";
    }
);

/**
 * Custom blade tag to import/enqueue stylesheet
 * Usage: @css('path/to/file.css')
 */
Blade::directive(
    'css',
    function ($expression) {
        return "<?php HTML::enqueueStyle{$expression}; ?>";
    }
);

/**
 * Custom blade tag to render queued javascript
 * Usage: @js
 */
Blade::directive(
    'printjs',
    function ($expression) {
        return "<?php foreach(HTML::getEnqueuedScripts() as \$src): echo HTML::script(\$src); endforeach; ?>";
    }
);

/**
 * Custom blade tag to render queued stylesheets
 * Usage: @css
 */
Blade::directive(
    'printcss',
    function ($expression) {
        return "<?php foreach(HTML::getEnqueuedStyles() as \$src): echo HTML::style(\$src); endforeach; ?>";
    }
);

/**
 * Custom blade tag to get render breadcrumbs
 * Usage: @title('Page Title')
 */
Blade::directive(
    'breadcrumbs',
    function ($expression) {
        return "<?= HTML::breadcrumb(); ?>";
    }
);

/**
 * Custom blade tag to add breadcrumb
 * Usage: @breadcrumb('Page Title', '/page-url')
 */
Blade::directive(
    'breadcrumb',
    function ($expression) {
        return "<?php HTML::breadcrumb({$expression}); ?>";
    }
);

/**
 * Custom blade tags to determine whether user has a certain role
 * Usage:
 * @role('role_slug')
 * @elseifrole('role_slug')
 *
 * @endrole
 */
Blade::directive(
    'role',
    function ($expression) {
        return "<?php if (visitor() && visitor()->is{$expression}) : ?>";
    }
);

Blade::directive(
    'elseifrole',
    function ($expression) {
        return "<?php elseif (visitor() && visitor()->is{$expression}) : ?>";
    }
);

Blade::directive(
    'endrole',
    function ($expression) {
        return "<?php endif; ?>";
    }
);

/**
 * Custom blade tags to determine whether user has a certain permission
 * Usage:
 * @ifrole('role_slug')
 * @elseifrole('role_slug')
 *
 * @endifrole
 */
Blade::directive(
    'can',
    function ($expression) {
        return "<?php if (visitor() && visitor()->can{$expression}) : ?>";
    }
);

Blade::directive(
    'elseifcan',
    function ($expression) {
        return "<?php elseif (visitor() && visitor()->can{$expression}) : ?>";
    }
);

Blade::directive(
    'endcan',
    function ($expression) {
        return "<?php endif; ?>";
    }
);

/**
 * Custom blade tags to determine whether user has a certain role
 * Usage:
 * @ifroute('route_name')
 * @elseifroute('route_name')
 *
 * @endifroute
 */
Blade::directive(
    'route',
    function ($expression) {
        return "<?php if (Route::currentRouteName() == {$expression}) : ?>";
    }
);

Blade::directive(
    'elseifroute',
    function ($expression) {
        return "<?php elseif (Route::currentRouteName() == {$expression}) : ?>";
    }
);

Blade::directive(
    'endroute',
    function ($expression) {
        return "<?php endif; ?>";
    }
);

/**
 * Custom blade tag to set variables
 * Usage: @title('Page Title')
 */
Blade::directive(
    'set',
    function ($expression) {
        // $pattern     = '#@set\(\s*[\'|"]([a-zA-Z_0-9]+)[\'|"]\s*,\s*(.*)\s*\)#';
        return "<?php {$expression}; ?>";
    }
);

/**
 * Custom blade tag to render a ReactJS component
 * Usage: @react('ComponentName', $arguments))
 */
Blade::directive(
    'react',
    function ($expression) {

        $expression = substr($expression, 1);
        $expression = substr($expression, 0, -1);

        $expressionParts = explode(',', $expression, 2);

        $componentName = $expressionParts[0];
        $componentName = trim($componentName, '\'"');
        $componentId   = $componentName  . '_' . str_random();
        $arguments     = isset($expressionParts[1]) ? $expressionParts[1] : '[]';

        return "
<div id=\"{$componentId}\"></div>
                <script>
                    ReactDOM.render(
                        React.createElement({$componentName}, <?php echo json_encode({$arguments}) ?>),
                        document.getElementById('{$componentId}')
                    );
                </script>
                ";
    }
);
