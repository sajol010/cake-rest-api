<?php

namespace RestApi\Controller;

use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Api error controller
 *
 * This controller will sets configuration to render errors
 */
class ApiErrorController extends AppController
{
    /**
     * beforeRender callback.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @return \Cake\Http\Response|null
     */
    public function beforeRender(EventInterface $event): ?Response
    {
        $this->httpStatusCode = $this->getResponse()->getStatusCode();
        $this->setResponse($this->getResponse()->withStatus($this->httpStatusCode));

        $this->apiResponse[$this->responseFormat['messageKey']] = Configure::read('ApiRequest.debug') && isset($this->viewVars['error'])
            ? $this->viewVars['error']->getMessage()
            : ($this->getResponse()->getReasonPhrase() ?: 'Unknown error!');
        Configure::write('apiExceptionMessage', isset($this->viewVars['error']) ? $this->viewVars['error']->getMessage() : null);
        parent::beforeRender($event);
        $this->viewBuilder()->setClassName('RestApi.ApiError');
        return null;
    }
}
