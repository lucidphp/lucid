<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template;

use SplStack;
use Lucid\Module\Template\Loader\LoaderInterface;
use Lucid\Module\Template\Resource\FileResource;
use Lucid\Module\Template\Resource\StringResource;
use Lucid\Module\Template\Resource\ResourceInterface;
use Lucid\Module\Template\IdentityParserInterface as Parser;
use Lucid\Module\Template\Exception\LoaderException;
use Lucid\Module\Template\Exception\RenderException;
use Lucid\Module\Template\Exception\TemplateException;
use Lucid\Module\Template\Extension\FunctionInterface;
use Lucid\Module\Template\Extension\ExtensionInterface;

/**
 * @class Engine
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Engine extends AbstractPhpEngine implements ViewAwareInterface
{
    const SUPPORT_TYPE = 'php';

    /**
     * encoding
     *
     * @var string
     */
    protected $encoding;

    /**
     * functions
     *
     * @var array
     */
    protected $functions;

    /**
     * globals
     *
     * @var array
     */
    protected $globals;

    /**
     * sections
     *
     * @var array
     */
    public $sections;

    /**
     * parent
     *
     * @var array
     */
    protected $parents;

    /**
     * proxy
     *
     * @var PhpRenderInterface|null
     */
    protected $proxy;

    /**
     * stack
     *
     * @var \SplStack
     */
    protected $stack;

    /**
     * errHandler
     *
     * @var string
     */
    protected $errHandler;

    /**
     * errFunc
     *
     * @var \Closure
     */
    protected $errFunc;

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader
     * @param TemplateIdentityInterface $identity
     * @param array $helpers
     */
    public function __construct(LoaderInterface $loader, Parser $parser = null, $enc = 'UTF-8')
    {
        $this->globals = [];
        $this->sections = [];
        $this->functions = [];
        $this->setEncoding($enc);
        $this->stack = new SplStack;

        parent::__construct($loader, $parser);
        $this->addType(self::SUPPORT_TYPE);

    }

    /**
     * setEncoding
     *
     * @param string $enc
     *
     * @return void
     */
    public function setEncoding($enc)
    {
        $this->encoding = $enc;
    }

    /**
     * setGlobals
     *
     * @param array $globals
     *
     * @return void
     */
    public function setGlobals(array $globals)
    {
        $this->globals = $globals;
    }

    /**
     * addGlobal
     *
     * @param mixed $key
     * @param mixed $parameter
     *
     * @return void
     */
    public function addGlobal($key, $parameter)
    {
        $this->globals[$key] = $parameter;
    }

    /**
     * Get global data.
     *
     * @return array
     */
    public function getGlobals()
    {
        return $this->globals;
    }

    /**
     * registerExtension
     *
     * @param ExtensionInterface $extension
     *
     * @return void
     */
    public function registerExtension(ExtensionInterface $extension)
    {
        $extension->setEngine($this);

        foreach ($extension->functions() as $func) {
            $this->registerFunction($func);
        }
    }

    /**
     * registerExtension
     *
     * @param ExtensionInterface $extension
     *
     * @return void
     */
    public function removeExtension(ExtensionInterface $extension)
    {
        foreach ($extension->functions() as $func) {
            unset($this->functions[$func->getName()]);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws RenderException
     */
    public function render($template, array $parameters = [])
    {
        $resource = $this->loadTemplate($template);

        $this->stack->push([
            $resource,
            $this->mergeShared($this->getParameters($template, $parameters))
        ]);

        $hash = $resource->getHash();

        if (isset($this->parents[$hash])) {
            throw new RenderException(sprintf('Circular reference in %s.', $template));
        }

        unset($this->parents[$hash]);

        $this->startErrorHandling();

        $content = $this->doRender();

        if (isset($this->parents[$hash])) {
            $content = $this->render($this->parents[$hash], $parameters);
        }

        $this->stopErrorHandling();

        $this->stack->pop();

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function display($template, array $parameters = [])
    {
        echo $this->render($template, $parameters);
    }

    /**
     * insert
     *
     * @param mixed $template
     * @param array $options
     *
     * @return void
     */
    public function insert($template, array $replacements = [])
    {
        list ($resource, $params) = $this->getCurrent();

        return $this->render($template, array_merge($params, $replacements));
    }

    /**
     * extend
     *
     * @param mixed $template
     *
     * @return void
     */
    public function extend($template)
    {
        list ($resource,) = $this->getCurrent();

        //if (isset($this->parents[$hash = $resource->getHash()])) {
            //throw new RenderException('Circular reference.');
        //}

        $this->parents[$resource->getHash()] = $template;
    }

    /**
     * hasParent
     *
     * @return boolean
     */
    protected function hasParent()
    {
        list ($resource, ) = $this->getCurrent();

        return isset($this->parents[$resource->getHash()]);
    }

    /**
     * getParameters
     *
     * @param mixed $template
     * @param array $parameters
     *
     * @return array
     */
    protected function getParameters($template, array $parameters)
    {
        if (null !== ($view = $this->getManager())) {
            $view->notifyListeners($name = $this->getIdentity()->identify($template)->getName());

            if ($data = $view->flushData($name)) {
                $parameters = $data->all($parameters);
            }
        }

        return $parameters;
    }

    /**
     * section
     *
     * @param mixed $name
     *
     * @return void
     */
    public function section($name)
    {
        if (isset($this->sections[$name])) {
            $section = $this->sections[$name];
            unset($this->sections[$name]);
        } else {
            $section = new Section($name);
        }

        $this->sections[$name] = $section;

        $section->start();
    }

    /**
     * endsection
     *
     * @return void
     */
    public function endsection()
    {
        $section = $this->getLastSection();
        $section->stop();

        if ($this->hasParent()) {
            return;
        }

        $content = $section->getContent(0);
        $section->reset();

        return $content;
    }

    /**
     * getCurrent
     *
     * @return array
     */
    protected function getCurrent()
    {
        return $this->stack->top();
    }

    ///**
    // * getRoot
    // *
    // * @return array
    // */
    //protected function getRoot()
    //{
    //    return $this->stack->bottom();
    //}

    /**
     * getLastSection
     *
     * @return Section
     */
    protected function getLastSection()
    {
        if (0 === count($this->sections)) {
            throw new RenderException('Cannot end a section. You must start a section first.');
        }

        $keys = array_keys($this->sections);
        $key = array_pop($keys);

        return $this->sections[$key];
    }

    /**
     * Register a function on the template engine.
     *
     * @param string $alias
     * @param callable $callable
     *
     * @return void
     */
    public function registerFunction(FunctionInterface $fn)
    {
        $this->functions[$fn->getName()] = $fn;
    }

    /**
     * execute
     *
     * @return void
     */
    public function func()
    {
        $args = func_get_args();
        $fns  = explode('|', array_shift($args));
        $res  = null;

        foreach (array_reverse($fns) as $fnc) {
            if (!isset($this->functions[$fnc])) {
                throw new \RuntimeException(sprintf('Template function "%s" does not exist.', $fnc));
            }

            $res = $this->functions[$fnc]->call($args);

            if (!$this->functions[$fnc]->getOption('is_safe_html')) {
                $res = $this->escape($res);
            }

            $args = [$res];
        }

        return $res;
    }

    /**
     * Escape a string
     *
     * @param mixed $string
     *
     * @return void
     */
    public function escape($string)
    {
        return htmlspecialchars($string, ENT_COMPAT, $this->getEncoding());
    }

    /**
     * doRender
     *
     *
     * @return void
     */
    protected function doRender()
    {
        list($resource, $parameters) = $this->getCurrent();

        ob_start();
        try {

            if ($resource instanceof FileResource) {
                $this->displayFile($resource, $parameters);
            } elseif ($resource instanceof StringResource) {
                $this->displayString($resource, $parameters);
            }

        } catch (\Exception $e) {
            ob_end_clean();

            if ($e instanceof TemplateException) {
                throw $e;
            }

            throw new RenderException($e->getMessage(), $e, $e->getCode());
        }

        return ob_get_clean();
    }

    /**
     * displayFile
     *
     * @param FileResource $resource
     * @param array $parameters
     *
     * @return void
     */
    protected function displayFile(FileResource $resource, array $parameters)
    {
        extract($parameters, EXTR_SKIP);
        include $resource->getResource();
    }

    /**
     * displayString
     *
     * @param StringResource $resource
     * @param array $parameters
     *
     * @return void
     */
    protected function displayString(StringResource $resource, array $parameters)
    {
        extract($parameters, EXTR_SKIP);
        eval('; ?>' . $resource->getContents() . '<?php ;');
    }

    /**
     * getProxy
     *
     * @return PhpRenderInterface
     */
    protected function getProxy()
    {
        return null === $this->proxy ? $this->proxy = new RenderEngineDecorator($this) : $this->proxy;
    }

    /**
     * getEncoding
     *
     * @return void
     */
    protected function getEncoding()
    {
        return $this->encoding ?: 'UTF-8';
    }

    /**
     * mergeShared
     *
     * @param array $params
     *
     * @return array
     */
    protected function mergeShared(array $params)
    {
        $proxy = $this->getProxy();

        return array_merge($this->globals, $params, ['view' => $proxy, 'func' => [$this, 'func']]);
    }

    /**
     * startErrorHandling
     *
     * @return void
     */
    protected function startErrorHandling()
    {
        $this->errHandler = set_error_handler($this->getErrFunc());
    }

    /**
     * Get the error handler
     *
     * @return \Closure
     */
    protected function getErrFunc()
    {
        if (null === $this->errFunc) {
            $this->errFunc = function ($errno, $errstr, $errfile, $errline) {
                $this->stopErrorHandling();
                throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
            };
        }

        return $this->errFunc;
    }

    /**
     * stopErrorHandling
     *
     * @return void
     */
    protected function stopErrorHandling()
    {
        if (null !== $this->errHandler) {
            restore_error_handler();
        }

        $this->errHandler = null;
    }
}
