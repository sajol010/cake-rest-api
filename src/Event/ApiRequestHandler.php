<?php

namespace RestApi\Event;

use Cake\Core\Configure;
use Cake\Event\EventListenerInterface;
use Cake\Http\Response;
use Cake\Event\EventInterface;
use Cake\Http\ServerRequest;
use RestApi\Utility\ApiRequestLogger;

/**
 * Event listner for API requests.
 *
 * This class binds the different events and performs required operations.
 */
class ApiRequestHandler implements EventListenerInterface
{
    /**
     * Event bindings.
     *
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Dispatcher.beforeDispatch' => [
                'callable' => 'beforeDispatch',
                'priority' => 0,
            ],
            'Dispatcher.afterDispatch' => [
                'callable' => 'afterDispatch',
                'priority' => 9999,
            ],
            'Controller.shutdown' => [
                'callable' => 'shutdown',
                'priority' => 9999,
            ],
        ];
    }

    /**
     * Handles incoming request and its data.
     *
     * @param EventInterface $event The beforeDispatch event
     */
    public function beforeDispatch(EventInterface $event)
    {
        /**
         * @var $response Response
         */
        $response = $this->buildResponse($event);
        Configure::write('requestLogged', false);
        $request = $event->getData('request');
        if (!$request instanceof ServerRequest) {
            return null;
        }
        if ('OPTIONS' === $request->getMethod()) {
            $event->stopPropagation();
            return $response->withStatus(200);
        }
//        if (empty($request->getData())) {
////            $request->data = $request->input('json_decode', true);
//        }
    }

    /**
     * Updates response headers.
     *
     * @param EventInterface $event The afterDispatch event
     */
    public function afterDispatch(EventInterface $event)
    {
        $this->buildResponse($event);
    }

    /**
     * Logs the request and response data into database.
     *
     * @param EventInterface $event The shutdown event
     */
    public function shutdown(EventInterface $event)
    {
        $controller = $event->getSubject();
        $request = $controller->getRequest();
        if ('OPTIONS' === $request->getMethod()) {
            return;
        }
        if (!Configure::read('requestLogged') && Configure::read('ApiRequest.log')) {
            if (Configure::read('ApiRequest.logOnlyErrors')) {
                $responseCode = $event->getSubject()->httpStatusCode;
                $logOnlyErrorCodes = Configure::read('ApiRequest.logOnlyErrorCodes');
                if (empty($logOnlyErrorCodes) && $responseCode !== 200) {
                    ApiRequestLogger::log($request, $controller->getResponse());
                } elseif (in_array($responseCode, $logOnlyErrorCodes)) {
                    ApiRequestLogger::log($request, $controller->getResponse());
                }
            } else {
                ApiRequestLogger::log($request, $controller->getResponse());
            }
        }
    }

    /**
     * Prepares the response object with content type and cors headers.
     *
     * @param EventInterface $event The event object either beforeDispatch or afterDispatch
     *
     * @return Response
     */
    private function buildResponse(EventInterface $event)
    {
        $request = $event->getData('request');
        $response = $event->getData('response');
        if (!$response instanceof Response) {
            return new Response();
        }
        if ('xml' === Configure::read('ApiRequest.responseType')) {
            $response = $response->withType('xml');
        } else {
            $response = $response->withType('json');
        }
        /**
         * @var $response Response
         */
        $response = $response->cors($request, Configure::read('ApiRequest.cors.origin'))
            ->allowMethods(array_unique(Configure::read('ApiRequest.cors.allowedMethods')))
            ->allowHeaders(Configure::read('ApiRequest.cors.allowedHeaders'))
            ->allowCredentials()
            ->maxAge(Configure::read('ApiRequest.cors.maxAge'))
            ->build();
        if (method_exists($event, 'setData')) {
            $event->setData('response', $response);
        }
        return $response;
    }
}
