<?php

namespace RestApi\Error;

use Cake\Core\Configure;
use Cake\Error\ExceptionRenderer;
use Cake\Utility\Xml;
use Cake\Http\Response;
use Throwable;
use RestApi\Controller\ApiErrorController;
use RestApi\Routing\Exception\InvalidTokenException;
use RestApi\Routing\Exception\InvalidTokenFormatException;
use RestApi\Routing\Exception\MissingTokenException;

/**
 * API Exception Renderer.
 *
 * Captures and handles all unhandled exceptions. Displays valid json response.
 */
class ApiExceptionRenderer extends ExceptionRenderer
{

    /**
     * Returns error handler controller.
     *
     * @return ApiErrorController
     */
    protected function _getController()
    {
        return new ApiErrorController();
    }

    /**
     * Handles MissingTokenException.
     *
     * @param MissingTokenException $exception MissingTokenException
     *
     * @return \Cake\Http\Response
     */
    public function missingToken($exception)
    {
        return $this->__prepareResponse($exception, ['customMessage' => true]);
    }

    /**
     * Handles InvalidTokenFormatException.
     *
     * @param InvalidTokenFormatException $exception InvalidTokenFormatException
     *
     * @return \Cake\Http\Response
     */
    public function invalidTokenFormat($exception)
    {
        return $this->__prepareResponse($exception, ['customMessage' => true]);
    }

    /**
     * Handles InvalidTokenException.
     *
     * @param InvalidTokenException $exception InvalidTokenException
     *
     * @return \Cake\Http\Response
     */
    public function invalidToken($exception)
    {
        return $this->__prepareResponse($exception, ['customMessage' => true]);
    }

    /**
     * Prepare response.
     *
     * @param Throwable $exception Exception
     * @param array $options Array of options
     *
     * @return \Cake\Http\Response
     */
    private function __prepareResponse($exception, $options = []): Response
    {
        $controller = $this->_getController();
        $response = $controller->getResponse();
        $code = $this->_code($exception);
        $response = $response->withStatus($code);

        Configure::write('apiExceptionMessage', $exception->getMessage());

        $responseFormat = $controller->responseFormat;
        $responseData = [
            $responseFormat['statusKey'] => !empty($options['responseStatus']) ? $options['responseStatus'] : $responseFormat['statusNokText'],
            $responseFormat['resultKey'] => [
                $responseFormat['errorKey'] => ($code < 500) ? 'Not Found' : 'An Internal Error Has Occurred.',
            ],
        ];

        if ((isset($options['customMessage']) && $options['customMessage']) || Configure::read('ApiRequest.debug')) {
            $responseData[$responseFormat['resultKey']][$responseFormat['errorKey']] = $exception->getMessage();
        }

        if ('xml' === Configure::read('ApiRequest.responseType')) {
            $response = $response->withType('xml')
                ->withStringBody(Xml::fromArray([Configure::read('ApiRequest.xmlResponseRootNode') => $responseData], 'tags')->asXML());
        } else {
            $response = $response->withType('json')
                ->withStringBody(json_encode($responseData));
        }

        $controller->setResponse($response);
        $this->controller = $controller;

        return $response;
    }
}
