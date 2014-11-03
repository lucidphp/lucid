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

use Lucid\Module\Template\Loader\LoaderInterface;
use Lucid\Module\Template\Resource\FileResource;
use Lucid\Module\Template\Resource\ResourceInterface;
use Lucid\Module\Template\IdentityParser as Parser;
use Lucid\Module\Template\Exception\RenderException;
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
     * helpers
     *
     * @var HelperInterface[]
     */
    protected $helpers;

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
    protected $sections;

    /**
     * renderParams
     *
     * @var mixed
     */
    protected $renderParams;

    /**
     * renderTemplate
     *
     * @var mixed
     */
    protected $renderTemplate;

    /**
     * parent
     *
     * @var mixed
     */
    protected $parents;

    /**
     * current
     *
     * @var mixed
     */
    protected $current;

    protected $proxy;

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader
     * @param TemplateIdentityInterface $identity
     * @param array $helpers
     */
    public function __construct(LoaderInterface $loader, Parser $parser = null, array $helpers = [])
    {
        $this->globals = [];
        $this->functions = [];
        $this->sections = [];
        $this->setHelpers($helpers);
        $this->setEncoding('UTF-8');
        $this->renderParams = [];

        parent::__construct($loader, $parser);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [self::SUPPORT_TYPE];
    }

    /**
     * {@inheritdoc}
     *
     * @throws RenderException
     */
    public function render($template, array $parameters = [])
    {
        $template = $this->loadTemplate($template);

        $this->current = $key = hash('sha256', serialize($template->getName()));
        $this->parents[$this->current] = null;

        // ensure warnings in the php template are catched properly
        set_error_handler(function ($errno, $errstr) {
            throw new RenderException($errstr);
        });


        if ($view = $this->getManager()) {
            $view->notifyListeners($template->getName());
        }

        $parameters = $this->getValidatedParameters($parameters);

        try {
            $content = $template->render($parameters);
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }

        restore_error_handler();

        if (isset($this->parents[$key])) {
            $content = $this->render($this->parents[$key], $parameters);
        }

        return $content;
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
        $this->parents[$this->current] = $template;
    }

    /**
     * insert
     *
     * @param mixed $template
     * @param array $options
     *
     * @return void
     */
    public function insert($template, array $vars = [], array $options = [])
    {
        echo $this->render($template, $this->pullOptions($vars, $options));
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
        if (!isset($this->sections[$name])) {
            $this->sections[$name] = '';
        }

        ob_start();
        ob_implicit_flush(0);
    }

    /**
     * endsection
     *
     * @return void
     */
    public function endsection()
    {
        if (0 === count($this->sections)) {
            throw new RenderException('Cannot end a section. You must start a section first.');
        }

        end($this->sections);
        $key = key($this->sections);

        if ($this->sections[$key]) {
            ob_end_clean();
            return $this->sections[$key];
        }

        return $this->sections[$key] = ob_get_clean();
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
     * Set a set of template helpers.
     *
     * @param array $helpers
     *
     * @return void
     */
    public function setHelpers(array $helpers)
    {
        $this->helpers = [];

        foreach ($helpers as $helper) {
            $this->addHelper($helper);
        }
    }

    /**
     * addHelper
     *
     * @param HelperInterface $helper
     *
     * @return void
     */
    public function addHelper(HelperInterface $helper)
    {
        $this->helpers[$helper->getName()] = $helper;
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
     * registerExtension
     *
     * @param ExtensionInterface $extension
     *
     * @return void
     */
    public function registerExtension(ExtensionInterface $extension)
    {
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
     * @param ResourceInterface $resource
     * @param array $parameters
     *
     * @return string|boolean false
     */
    protected function doRender(ResourceInterface $resource, array $parameters)
    {
        $content = '';

        if ($this->renderTemplate === $resource) {
            throw new RenderException('dead it is.');
        }

        $this->renderTemplate = $resource;

        $this->renderParams = $parameters = $this->getValidatedParameters($parameters);

        if ($resource instanceof FileResource) {
            // start the output buffer
            ob_start();
            extract($parameters, EXTR_SKIP);

            include $resource->getResource();

            return ob_get_clean();
        }

        try {
            ob_start();
            extract($parameters, EXTR_SKIP);
            eval('; ?>' . $resource->getContents() . '<?php ;');

            return ob_get_clean();

        } catch (\Exception $e) {
            throw new RenderException('Counld not render template becuase errors', $e);
        }

        return false;
    }

    protected function getProxy()
    {
        return null === $this->proxy ? $this->proxy = new RenderEngineProxy($this) : $this->proxy;
    }

    /**
     * getValidatedParameters
     *
     * @param array $parameters
     *
     * @return array
     */
    protected function getValidatedParameters(array $parameters)
    {
        $parameters = array_merge($this->globals, $parameters);

        $proxy = $this->getProxy();

        foreach (['view', 'this'] as $keyWord) {
            //if (isset($parameters[$keyWord]) && $this !== $parameters[$keyWord]) {
            //    throw RenderException::invalidParameter($keyWord);
            //}

            if ('func' !== $keyWord && !isset($parameters[$keyWord])) {
                $parameters[$keyWord] = $proxy;
            }
        }

        if (!isset($parameters['func'])) {
            $parameters['func'] = [$proxy, 'func'];
        }

        return array_merge($this->renderParams, $parameters);
    }

    /**
     * pullOptions
     *
     * @param array $vars
     * @param array $options
     *
     * @return array
     */
    protected function pullOptions(array $vars, array $options)
    {
        $vars = array_intersect_key($this->renderParams, array_flip($vars));

        if (0 === count($options)) {
            $options = $this->renderParams;
        };

        return array_merge($options, $vars);
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
     * getDefaultHelpers
     *
     * @return void
     */
    protected function getDefaultHelpers()
    {
        return [
            new InsertHelper,
            new ExtendHelper
        ];
    }
}
