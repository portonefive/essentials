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

Blade::directive(
    'meta',
    function ($expression) {
        return "<?php HTML::addMetaData{$expression}; ?>";
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
 * Custom blade tag to render queued stylesheets
 * Usage: @css
 */
Blade::directive(
    'printmeta',
    function ($expression) {
        $replacement = "<?php foreach(HTML::getMetaData() as \$name => \$content) : ";
        $replacement .= "echo \"<meta property=\\\"{\$name}\\\" content=\\\"{\$content}\\\">\";";
        $replacement .= "endforeach; ?>";

        return $replacement;
    }
);

/**
 * Custom blade tag to set/override page title
 * Usage: @title('Page Title')
 */
Blade::directive(
    'title',
    function ($expression) {
        return "<?php HTML::setPageTitle{$expression}; ?>";
    }
);

/**
 * Custom blade tag to set/override page title
 * Usage: @title('Page Title')
 */
Blade::directive(
    'titlevar',
    function ($expression) {
        return "<?php HTML::setPageTitleVariable{$expression}; ?>";
    }
);

/**
 * Custom blade tag to get page title
 * Usage: @title('Page Title')
 */
Blade::directive(
    'printtitle',
    function ($expression) {
        return "<?= HTML::getPageTitle(); ?>";
    }
);

/**
 * Custom blade tag to get render breadcrumbs
 * Usage: @title('Page Title')
 */
Blade::directive(
    'breadcrumbs',
    function ($expression) {
        return "<?= HTML::renderBreadcrumbs(); ?>";
    }
);

/**
 * Custom blade tag to add breadcrumb
 * Usage: @breadcrumb('Page Title', '/page-url')
 */
Blade::directive(
    'breadcrumb',
    function ($expression) {
        return "<?php HTML::addBreadcrumb{$expression}; ?>";
    }
);

/**
 * Custom blade tags to determine whether user has a certain role
 * Usage:
 * @ifrole('role_slug')
 * @elseifrole('role_slug')
 *
 * @endifrole
 */
Blade::directive(
    'ifrole',
    function ($expression) {
        return "<?php if (\$visitor->is{$expression}) : ?>";
    }
);

Blade::directive(
    'elseifrole',
    function ($expression) {
        return "<?php elseif (\$visitor->is{$expression}) : ?>";
    }
);

Blade::directive(
    'endifrole',
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
    'ifcan',
    function ($expression) {
        return "<?php if (\$visitor->can{$expression}) : ?>";
    }
);

Blade::directive(
    'elseifcan',
    function ($expression) {
        return "<?php elseif (\$visitor->can{$expression}) : ?>";
    }
);

Blade::directive(
    'endifcan',
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
    'ifroute',
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
    'endifroute',
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