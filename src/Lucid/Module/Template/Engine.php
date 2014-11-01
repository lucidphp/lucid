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
use Lucid\Module\Template\TemplateIdentityInterface as Identity;
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
class Engine extends AbstractEngine
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
     * Constructor.
     *
     * @param LoaderInterface $loader
     * @param TemplateIdentityInterface $identity
     * @param array $helpers
     */
    public function __construct(LoaderInterface $loader, Identity $identity = null, array $helpers = [])
    {
        $this->globals = [];
        $this->functions = [];
        $this->sections = [];
        $this->setHelpers($helpers);
        $this->setEncoding('UTF-8');
        $this->renderParams = [];

        parent::__construct($loader, $identity);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::SUPPORT_TYPE;
    }

    /**
     * {@inheritdoc}
     *
     * @throws RenderException
     */
    public function render($template, array $parameters = [])
    {
        $resource = $this->load($template);

        if (!$content = $this->doRender($resource, $parameters)) {
            throw new RenderException(sprintf('Counld not render template "%s".', $template->getName()));
        }

        return $content;
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
        $this->sections[$name] = '';
        ob_start();
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

        return $this->sections[key($this->sections)] = ob_get_clean();
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
     * insert
     *
     * @param mixed $template
     * @param array $options
     *
     * @return void
     */
    public function insert($template, array $vars = [], array $options = [])
    {
        return $this->render($template, $options);
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
        $parameters = $this->getValidatedParameters($parameters);

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
            //throw new RenderException('Counld not render template', $e);
        }

        return false;
    }

    protected function getValidatedParameters(array $parameters)
    {
        $parameters = array_merge($this->globals, $parameters);

        foreach (['view', 'this', 'func'] as $keyWord) {
            if (isset($parameters[$keyWord]) && $this !== $parameters[$keyWord]) {
                throw RenderException::invalidParameter($keyWord);
            }

            if ('func' !== $keyWord) {
                $parameters[$keyWord] = $this;
            }
        }

        if (!isset($parameters['func'])) {
            $parameters['func'] = [$this, 'func'];
        }

        return $parameters;
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
}
