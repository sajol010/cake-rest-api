<?php

namespace RestApi\Controller;

use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * Api Controller
 *
 * Provides basic functionality for building REST APIs
 */
class ApiController extends AppController
{

    /**
     * Before render callback.
     *
     * @param \Cake\Event\EventInterface $event The beforeRender event.
     * @return \Cake\Http\Response|null
     */
    public function beforeRender(EventInterface $event): ?Response
    {
        $this->viewBuilder()->setClassName('RestApi.Api');

        return parent::beforeRender($event);
    }
}