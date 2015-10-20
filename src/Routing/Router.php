<?php

namespace PortOneFive\Essentials\Routing;

use HTML;
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

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        //$response->headers->set('Access-Control-Allow-Headers', 'X-XSRF-TOKEN');

        if ($request->isMethod('options')) {
            $headers = [
                //'Access-Control-Allow-Origin'  => '*',
                'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, X-Auth-Token, Origin, Authorization, X-XSRF-TOKEN'
            ];

            return response('You are connected to the API', 200, $headers);
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
                return [
                    view($layout, ['html' => $contents, '_originalTemplateName' => $view->getName()], $view->getData()),
                    $view->getFactory()->getSections()
                ];
            }
        );

        if (is_array($sections)) {
            foreach ($sections as $sectionId => $sectionContent) {
                $response->getFactory()->startSection($sectionId, $sectionContent);
            }
        }

        if ($request instanceof Request && $request->wantsJson()) {
            $response = [
                'messages' => messages()->all(),
                'title'    => ! empty($response['title']) ? $response['title'] : null,
                'data'     => $response->getData(),
                'response' => [
                    'html'     => $response['html'],
                    'sections' => is_array($sections) ? $sections : [],
                ]
            ];
        }

        return $response;
    }
}
