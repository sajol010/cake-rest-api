<?php
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Event\EventInterface;
use Cake\Http\MiddlewareQueue;
use RestApi\Middleware\RestApiMiddleware;
EventManager::instance()->on('Application.buildMiddleware', function (EventInterface $event, MiddlewareQueue $middleware) {
    $middleware->add(new RestApiMiddleware());
});
/*
 * Read configuration file and inject configuration
 */
try {
    Configure::load('RestApi.api', 'default', false);
    Configure::load('api', 'default', true);
} catch (Exception $e) {
    // nothing
}
// Set default response format
if (!in_array(Configure::read('ApiRequest.responseType'), ['json', 'xml'])) {
    Configure::write('ApiRequest.responseType', 'json');
}