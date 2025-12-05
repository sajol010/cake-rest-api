<?php

namespace RestApi\Utility;

use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;

/**
 * Handles the request logging.
 */
class ApiRequestLogger
{
    /**
     * Logs the request and response data into database.
     *
     * @param ServerRequest $request The \Cake\Http\ServerRequest object
     * @param Response $response The \Cake\Http\Response object
     */
    public static function log(ServerRequest $request, Response $response)
    {
        Configure::write('requestLogged', true);
        try {
            $apiRequests = TableRegistry::getTableLocator()->get('RestApi.ApiRequests');
            $entityData = [
                'http_method' => $request->getMethod(),
                'endpoint' => $request->getRequestTarget(),
                'token' => Configure::read('accessToken'),
                'ip_address' => $request->clientIp(),
                'request_data' => json_encode($request->getData()),
                'response_code' => $response->getStatusCode(),
                'response_type' => Configure::read('ApiRequest.responseType'),
                'response_data' => (string)$response->getBody(),
                'exception' => Configure::read('apiExceptionMessage'),
            ];
            $entity = $apiRequests->newEntity($entityData);
            $apiRequests->save($entity);
        } catch (\Exception $e) {
            return;
        }
    }
}
