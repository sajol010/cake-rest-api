<?php

namespace RestApi\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Firebase\JWT\JWT;
use RestApi\Routing\Exception\InvalidTokenException;
use RestApi\Routing\Exception\InvalidTokenFormatException;
use RestApi\Routing\Exception\MissingTokenException;

/**
 * Access control component class.
 *
 * Handles user authentication and permission.
 */
class AccessControlComponent extends Component
{
    /**
     * beforeFilter method.
     *
     * Handles request authentication using JWT.
     *
     * @param EventInterface $event The startup event
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        $result = true;
        if (Configure::read('ApiRequest.jwtAuth.enabled')) {
            $result = $this->_performTokenValidation($event);
        }
        $event->setResult($result);
    }

    /**
     * Performs token validation.
     *
     * @param EventInterface $event The startup event
     * @return bool
     */
    protected function _performTokenValidation(EventInterface $event)
    {
        $controller = $event->getSubject();
        $request = $controller->getRequest();
        if (!empty($request->getParam('allowWithoutToken')) && $request->getParam('allowWithoutToken')) {
            return true;
        }
        $token = '';
        $header = $request->getHeaderLine('Authorization');
        if (!empty($header)) {
            $parts = explode(' ', $header);
            if (count($parts) < 2 || empty($parts[0]) || !preg_match('/^Bearer$/i', $parts[0])) {
                throw new InvalidTokenFormatException();
            }
            $token = $parts[1];
        } elseif (!empty($request->getQuery('token'))) {
            $token = $request->getQuery('token');
        } elseif (!empty($request->getParam('token'))) {
            $token = $request->getParam('token');
        } else {
            throw new MissingTokenException();
        }
        try {
            $payload = JWT::decode($token, Configure::read('ApiRequest.jwtAuth.cypherKey'), [Configure::read('ApiRequest.jwtAuth.tokenAlgorithm')]);
        } catch (\Exception $e) {
            throw new InvalidTokenException();
        }
        if (empty($payload)) {
            throw new InvalidTokenException();
        }
        $controller->jwtPayload = $payload;
        $controller->jwtToken = $token;
        Configure::write('accessToken', $token);
        return true;
    }
}
