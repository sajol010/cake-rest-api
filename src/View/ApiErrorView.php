<?php
namespace RestApi\View;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\View\View;
/**
 * Api Error View
 *
 * Default view class for error
 */
class ApiErrorView extends View
{
    /**
     * Tracks whether the view has already been rendered.
     *
     * @var bool
     */
    protected bool $_hasRendered = false;

    /**
     * Layout
     *
     * @var string
     */
    protected $_responseLayout = 'error';
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        if ('xml' === Configure::read('ApiRequest.responseType')) {
            $this->setResponse($this->getResponse()->withType('xml'));
            $this->_responseLayout = 'xml_error';
        } else {
            $this->setResponse($this->getResponse()->withType('json'));
        }
    }
    /**
     * Renders custom api error view
     *
     * @param string|null $view Name of view file to use
     * @param string|null $layout Layout to use.
     * @return string|null Rendered content or null if content already rendered and returned earlier.
     * @throws Exception If there is an error in the view.
     */
    public function render(?string $view = null, string|false|null $layout = null): string
    {
        if ($this->_hasRendered) {
            return (string)$this->Blocks->get('content');
        }
        $this->setLayout("RestApi.{$this->_responseLayout}");
        $this->Blocks->set('content', $this->renderLayout('', $this->getLayout()));
        $this->_hasRendered = true;
        return (string)$this->Blocks->get('content');
    }
}
