<?php

namespace PortOneFive\Essentials\Routing;

use HTML;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Router extends \Illuminate\Routing\Router
{

    /**
     * Create a response instance from the given value.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @param  mixed                                     $response
     *
     * @return \Illuminate\Http\Response
     */
    public function prepareResponse($request, $response)
    {
        if ($response instanceof PsrResponseInterface) {
            $response = (new HttpFoundationFactory)->createResponse($response);
        } elseif ( ! $response instanceof SymfonyResponse) {

            if ($response instanceof View) {
                $response = $this->prepareViewResponse($request, $response);
            }

            $response = new Response($response);
        }

        return $response->prepare($request);
    }

    /**
     * @param $request
     * @param $response
     *
     * @return View
     */
    protected function prepareViewResponse($request, $response)
    {
        $controllerClassName = $request->route()->getAction()['controller'];
        $controllerClassName = substr($controllerClassName, 0, strpos($controllerClassName, '@'));

        $controller = app()->make($controllerClassName);
        $layout     = $controller->getLayout();

        /** @var View $response */

        list($response, $sections) = $response->render(
            function ($view, $contents) use ($layout) {

                $sections = $view->getFactory()->getSections();
                $content  = view($layout, ['html' => $contents], $view->getData());

                if (is_array($sections)) {
                    foreach ($sections as $sectionId => $sectionContent) {
                        $content->getFactory()->inject($sectionId, $sectionContent);
                    }
                }

                return [$content, $sections];
            }
        );

        if ($request instanceof Request && $request->wantsJson()) {
            $response = [
                'messages' => messages()->all(),
                'title'    => ! empty($response['title']) ? $response['title'] : null,
                'response' => [
                    'html'     => $response['html'],
                    'sections' => is_array($sections) ? $sections : [],
                ]
            ];
        }

        return $response;
    }
}