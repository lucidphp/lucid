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

use Lucid\Module\Template\Resource\FileResource;
use Lucid\Module\Template\Exception\RenderException;

/**
 * @class Template
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Template implements TemplateInterface
{
    /**
     * engine
     *
     * @var mixed
     */
    private $engine;

    /**
     * identity
     *
     * @var mixed
     */
    private $identity;

    /**
     * Constructor.
     *
     * @param IdentityInterface $identity
     *
     * @return void
     */
    public function __construct(EngineInterface $engine, IdentityInterface $identity)
    {
        $this->engine = $engine;
        $this->identity = $identity;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->identity->getName();
    }

    /**
     * getTemplatePath
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->identity->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function render(array $parameters = [])
    {
        try {
            ob_start();

            $this->display($parameters);

            return ob_get_clean();
        } catch (\Exception $e) {
            ob_end_clean();
            if ($e instanceof RenderException) {
                throw $e;
            }

            throw new RenderException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function display(array $parameters)
    {
        echo $this->doRender($parameters);
    }

    /**
     * setEngine
     *
     * @param EngineInterface $engine
     *
     * @return void
     */
    public function setEngine(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * getEngine
     *
     *
     * @return void
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * setTemplateResource
     *
     * @param ResourceInterface $resource
     *
     * @return void
     */
    public function setTemplateResource(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * doRender
     *
     * @param ResourceInterface $resource
     * @param array $parameters
     *
     * @return string|boolean false
     */
    protected function doRender(array $parameters)
    {
        if ($this->identity->isFile()) {
            // start the output buffer
            ob_start();
            extract($parameters, EXTR_SKIP);

            include $this->identity->getResource()->getResource();

            return ob_get_clean();
        }

        try {
            ob_start();
            extract($parameters, EXTR_SKIP);
            eval('; ?>' . $this->identity->getResource()->getContents() . '<?php ;');

            return ob_get_clean();

        } catch (\Exception $e) {
            throw new RenderException('Counld not render template because of errors', $e);
        }

        return false;
    }
}
