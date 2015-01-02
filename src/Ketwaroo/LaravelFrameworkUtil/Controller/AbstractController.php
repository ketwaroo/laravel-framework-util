<?php

/**
 * 
 */

namespace Ketwaroo\LaravelFrameworkUtil\Controller;

use \Illuminate\Routing\Controller as LaravelController;
use Ketwaroo\LaravelFrameworkUtil\Constant;

/**
 * Description of ControllerAbstract
 *
 * @author Yaasir Ketwaroo <ketwaroo.yaasir@gmail.com>
 */
abstract class AbstractController extends LaravelController
{

    use \Ketwaroo\LaravelFrameworkUtil\Patterns\TraitBaseModel;

    const DEFAULT_ACTION = 'actionIndex';

    /**
     *
     * @var Template 
     */
    protected $layout;

    /**
     *
     * @var \Request
     */
    protected $request;
    protected $currentControllerUri,
            $currentAction,
            $currentActionUri,
            $requestParams = [];

    /**
     *
     * @var string namespaced vendor/package::path.to.view
     */
    protected $viewFile;

    public function __construct(\Request $request)
    {
        $this->request = $request;
        $this->init();
        // allow adding events at controller init
        \Event::fire(Constant::EVENT_CONTROLLER_INIT, [$this]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    abstract public function checkAuth();

    protected function init()
    {
        
    }

    /**
     * called just before action is called
     */
    protected function parseRequestParams()
    {
        $seg = \Request::segments();

        $seg = \Ketwaroo\LaravelFrameworkUtil\Filter\Filter::instance()->render(
                \Ketwaroo\LaravelFrameworkUtil\Constant::FILTERCONTENT_CONTROLLER_REQUESTSEGMENT
                , $seg
                , $this->getCurrentControllerUri()
                , $this->getCurrentActionUri()
        );

        $this->setRequestParams($seg);
    }

    /**
     * 
     * @return string|boolean value of first request key or false.
     */
    public function getFirstRequestParam($default = NULL)
    {
        $p   = $this->getRequestParams();
        $val = reset($p); // get first.
        return $val === FALSE ? $default : $val;
    }

    /**
     * get the url request param by key
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public function getRequestParam($key, $default = NULL)
    {
        $p = $this->getRequestParams();
        return array_get($p, $key, $default);
    }

    /**
     * 
     * @return array
     */
    protected function getRequestParams()
    {
        return $this->requestParams;
    }

    /**
     * 
     * @param array $params
     * @return \Ketwaroo\LaravelFrameworkUtil\Controller\AbstractController
     */
    protected function setRequestParams($params)
    {
        $this->requestParams = $params;
        return $this;
    }

    /**
     * 
     * @param type $currentControllerUri
     * @return \Ketwaroo\LaravelFrameworkUtil\Controller\AbstractController
     */
    public function setCurrentControllerUri($currentControllerUri)
    {
        $this->currentControllerUri = $currentControllerUri;
        return $this;
    }

    /**
     * 
     * @param string $currentAction methodName being used for current request
     * @return \Ketwaroo\LaravelFrameworkUtil\Controller\AbstractController
     */
    public function setCurrentAction($currentAction)
    {

        $this->currentAction = $currentAction;
        return $this;
    }

    /**
     * get the url segment that would correspond to current controller.
     * @return string
     */
    public function getCurrentControllerUri()
    {
        return $this->currentControllerUri;
    }

    /**
     * get the url segment that would correspond to current action.
     * @return string
     */
    public function getCurrentActionUri()
    {
        if(!isset($this->currentActionUri))
        {
            $this->currentActionUri = \Ketwaroo\LaravelFrameworkUtil\Text::toLowerDash(preg_replace('~^action~', '', $this->getCurrentAction()));
        }

        return $this->currentActionUri;
    }

    /**
     * 
     * @return string action method name
     */
    public function getCurrentAction()
    {
        if(empty($this->currentAction))
        {
            return $this->getDefaultAction();
        }
        return $this->currentAction;
    }

    protected function setupLayout()
    {
        if($this->isLayoutSetup())
        {
            return;
        }

        $templatePackage = $this->getView()->getPackageName();

        $this->layout = $this->getView()
                ->addJs('jquery', "{$templatePackage}::js/jquery.min.js")
                ->addJs('jquery-migrate', "{$templatePackage}::js/jquery-migrate.min.js", array('jquery'))
                //->addJs('jquery-ui', "{$templatePackage}::js/jquery-ui.min.js", array('jquery'))
                ->addCss('bootstrap-css', "{$templatePackage}::css/bootstrap.min.css")
                ->addJs('bootstrap-js', "{$templatePackage}::js/bootstrap.min.js", array('jquery'));
        ;

        $this->isLayoutSetup(TRUE);
    }

    /**
     * renders the default view.
     * @param array $params
     * @param string $viewFile [vendor/package::]path.to.view
     * @return string | \Illuminate\View\View
     */
    public function render($params = array(), $viewFile = NULL)
    {
        if(empty($viewFile))
        {
            $viewFile = $this->getViewFile();
        }
        return $this->getView()->render($viewFile, $params);
    }

    /**
     * get/set layout setup flag
     *
     * @param boolean|null $toggleTo
     * @return boolean
     */
    protected function isLayoutSetup($toggleTo = NULL)
    {
        return $this->_toggleOrGetField(__FUNCTION__, $toggleTo);
    }

    /**
     * 
     * @return Template
     */
    public function getView()
    {
        return $this->layout;
    }

    /**
     * if $layout = null, uses default html5 layout
     * if false, it will skip the parent layout use for ajax requests.
     * 
     * $layoutName is in the vendor/package::path.to.layoutfile
     * 
     * @param string|null|boolean $layoutName if false, disables the layout, null uses default otherwise namspaced path to file.
     * @return \Ketwaroo\LaravelFrameworkUtil\Controller\AbstractController
     */
    public function setViewLayout($layoutName)
    {
        $this->getView()->setLayoutName($layoutName);
        return $this;
    }

    /**
     * 
     * @return string vendor/package::path.to.view
     */
    public function getViewFile()
    {
        return $this->viewFile;
    }

    /**
     * 
     * @param string $view vendor/package::path.to.view
     * @return \Ketwaroo\LaravelFrameworkUtil\Controller\AbstractController
     */
    public function setViewFile($view)
    {
        $this->viewFile = $view;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getDefaultAction()
    {
        return static::DEFAULT_ACTION;
    }

    public function missingMethod($parameters = array())
    {
        if(method_exists($this, $this->getDefaultAction()))
        {
            $this->setCurrentAction($this->getDefaultAction());

            return $this->callAction($this->getDefaultAction(), $parameters);
        }
        else
        {
            \App::abort(404, 'action not found.');
        }
    }

    /**
     * Execute an action on the controller.
     * @param string $method
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        // detect and set actual action being used.
        if(!method_exists($this, $method))
        {
            $method = 'actionIndex';
        }

        $this->setCurrentAction($method);

        // Should checkAuth return anything, just return that.
        if(($test = $this->checkAuth()))
        {
            return $test;
        }

        // parse request segments just before calling action for any last minute processing.
        $this->parseRequestParams();

        return parent::callAction($method, $parameters);
    }

//    public function __call($method, $parameters)
//    {
//        return $this->missingMethod($parameters);
//    }

    /**
     * 
     * @return type
     */
    public function getControllerName()
    {
        return \Ketwaroo\LaravelFrameworkUtil\Text::toLowerDash(basename(\Ketwaroo\LaravelFrameworkUtil\File::unixifyPath(get_called_class())));
    }

    /**
     * generates a full url relative to the current controller
     * @param type $path
     * @param type $params
     * @return type
     */
    protected function selfUrl($path = '', $params = array())
    {
        // tricky
        //@todo need to refactor. may have added cleaner method up stream of this code.

        $controller = \Route::current()->parameter('controller');
        if(!empty($controller))
        {
            return $this->getUrl($controller, $path, $params);
        }

        return '';
    }

    /**
     * 
     * @param type $controller
     * @param type $path
     * @param type $params
     * @return type
     */
    public function getUrl($controller, $path = '', $params = array())
    {
        return url("$controller/$path", $params);
    }

    /**
     * cleanly retrives an error MessageBag from the request
     * normally would have to test if array or viewerrorbag and other messiness.
     * 
     * @param string $key
     * @return \Illuminate\Support\MessageBag
     */
    protected function getViewErrors($key = 'default')
    {
        $err = $this->getViewErrorBag();

        if(!($err->hasBag($key)))
        {
            $err->put($key, new \Illuminate\Support\MessageBag([]));
        }

        return $err->getBag($key);
    }

    /**
     * get the current View error bag or empty one.
     * @return \Illuminate\Support\ViewErrorBag
     */
    public function getViewErrorBag()
    {
        $errors = \Session::get('errors');

        if(!($errors instanceof \Illuminate\Support\ViewErrorBag)) // get some defaults.
        {
            $err = new \Illuminate\Support\ViewErrorBag();
        }
        else
        {
            $err = $errors;
        }

        return $err;
    }

}
