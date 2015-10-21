<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

use Lucid\Writer\Writer;
use Lucid\Writer\GeneratorInterface;

/**
 * @class AbstractWriter
 * @see GeneratorInterface
 * @abstract
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractWriter implements GeneratorInterface
{
    use ImportHelper;

    const I_NATIVE = 890;
    const I_HHVM   = 891;
    const N_SEP = '\\';

    /**
     * locked
     *
     * @var string
     */
    private $fqn;

    /**
     * locked
     *
     * @var string
     */
    private $name;

    /**
     * namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * methods
     *
     * @var array
     */
    private $methods;

    /**
     * uses
     *
     * @var array
     */
    protected $uses;

    /**
     * locked
     *
     * @var boolean
     */
    private $locked;

    /**
     * type
     *
     * @var int
     */
    private $type;

    /**
     * types
     *
     * @var array
     */
    private static $types = [
        T_CLASS     => 'class',
        T_TRAIT     => 'trait',
        T_INTERFACE => 'interface'
    ];

    /**
     * docTypes
     *
     * @var array
     */
    private static $docTypes = [
        self::I_NATIVE => '<?php',
        self::I_HHVM   => '<?hh'
    ];

    /**
     * blocks
     *
     * @var array
     */
    protected $blocks;

    /**
     * noAutoGenerate
     *
     * @var boolen
     */
    private $noAutoGenerateTag;

    /**
     * Constructor.
     *
     * @param string      $name
     * @param string|null $namespace
     */
    public function __construct($name, $namespace = null, $type = null)
    {
        $this->setNamespace($namespace);
        $this->setName($name);
        $this->type = $type;

        $this->uses = [];
        $this->blocks = [];
        $this->methods = [];

        $this->noAutoGenerateTag = false;
    }

    /**
     * setDocType
     *
     * @param mixed $type
     *
     * @return void
     */
    public function setDocType($type = self::I_NATIVE)
    {
    }

    /**
     * Get the interface|class|trait name.
     *
     * @return string the name
     */
    final public function getName()
    {
        return $this->name;
    }

    /**
     * Get the namespace
     *
     * @return string the namespace `NULL` if none.
     */
    final public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Get the full qualified name
     *
     * @return string the full qualified name as string
     */
    public function getFqn()
    {
        return $this->fqn;
    }

    /**
     * Get the document block
     *
     * @return DocBlock the docblock
     */
    public function getDoc()
    {
        if (!isset($this->blocks['doc'])) {
            $this->blocks['doc'] = new DocBlock;
        }

        return $this->blocks['doc'];
    }

    /**
     * Get the class|interface|trait description
     *
     * @return DocBlock
     */
    public function getObjDoc()
    {
        if (!isset($this->blocks['obj'])) {
            $this->blocks['obj'] = new DocBlock;
        }

        return $this->blocks['obj'];
    }

    /**
     * Switch off auto generation of parameter annotaions.
     *
     * @return void
     */
    public function noAutoGenerateTag()
    {
        $this->noAutoGenerateTag = true;
    }

    /**
     * Set methods.
     *
     * @param array $methods
     *
     * @return void
     */
    public function setMethods(array $methods)
    {
        $this->methods = [];

        foreach ($methods as $method) {
            $this->addMethod($method);
        }
    }

    /**
     * Add a use statement.
     *
     * @param string $use
     *
     * @return void
     */
    public function addUseStatement($use)
    {
        $this->addToImportPool($this->uses, $use);
    }

    /**
     * Shortcut for `addUseStatement()`
     *
     * @see ObjectWriter::addUseStatement()
     */
    public function addImport($use)
    {
        return $this->addUseStatement($use);
    }

    /**
     * Add a method.
     *
     * @param MethodInterface $method
     *
     * @return void
     */
    public function addMethod(MethodInterface $method)
    {
        $this->methods[] = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = self::RV_STRING)
    {
        $writer = new Writer;

        $this->prepareDoc($ddoc = clone($this->getDoc()));
        $this->prepareObjDoc($odoc = clone($this->getObjDoc()));

        $writer
            ->writeln('<?php');

        if (!$ddoc->isEmpty()) {
            $writer
                ->newline()
                ->writeln($ddoc->generate());
        }

        $writer
            ->newline()
            ->writeln(sprintf('namespace %s;', $ns = $this->getNamespace()));

        $this->writeUseStatements($writer, $this->getImportResolver(), null !== $ns);

        $writer
            ->newline()
            ->writeln($odoc->generate());

        $this->writeObject($writer);

        return $raw ? $writer : $writer->dump();
    }

    /**
     * resetBlocks
     *
     * @return void
     */
    private function replaceBlocks()
    {
        $ddoc = clone($this->getDoc());
        $odoc = clone($this->getObjDoc());

        $this->blocks = [
            'doc' => $ddoc,
            'obj' => $odoc
        ];
    }

    /**
     * trimNs
     *
     * @param string $name
     *
     * @return string
     */
    public static function trimNs($name)
    {
        return trim($name, self::N_SEP);
    }

    /**
     * writeBody
     *
     * @param Writer $writer
     *
     * @access protected
     * @return mixed
     */
    protected function writeObject(Writer $writer)
    {
        $writer
            ->writeln($this->getTypePrefix() . $this->getType().' '.$this->getName());

        $writer->appendln($this->getObjectDeclarationExtension())
            ->writeln('{');

        $this->writeObjectBody($writer)
            //->outdent()
            ->writeln('}'.PHP_EOL);
    }

    /**
     * tagAutogenerated
     *
     * @return boolean
     */
    protected function tagAutogenerated()
    {
        return !$this->noAutoGenerateTag;
    }

    /**
     * writeDoc
     *
     * @param Writer $writer
     *
     * @return void
     */
    protected function prepareDoc(DocBlock $doc)
    {
        if ($this->tagAutogenerated()) {
            $doc->setDescription(
                $this->getAutoGenerateTag() . ($doc->hasDescription() ? PHP_EOL . $doc->getDescription() : '')
            );
        }
    }

    /**
     * prepareObjDoc
     *
     * @param DocBlock $doc
     *
     * @return void
     */
    abstract protected function prepareObjDoc(DocBlock $doc);

    /**
     * getType
     *
     * @throws \LogicException
     * @return void
     */
    protected function getType()
    {
        try {
            return self::$types[$this->type];
        } catch (\Exception $e) {
            $types = array_values(self::$types);

            $type = array_pop($types);
            $types[] = 'or ' . $type;

            throw new \LogicException(sprintf('Invalid object type, type must be %s', implode(', ', $types)));
        }
    }

    /**
     * getTypePrefix
     *
     * @return null|string
     */
    protected function getTypePrefix()
    {
        return null;
    }

    /**
     * getUseStatements
     *
     * @param Writer $writer
     * @param boolean $newLine
     *
     * @return void
     */
    protected function writeUseStatements(Writer $writer, ImportResolver $resolver, $newLine = false)
    {
        if ($newLine) {
            $writer->newline();
        }

        $uses = [];

        foreach (array_unique($this->getImports()) as $use) {

            if ($this->inNamespace($use) && !$resolver->hasAlias($use)) {
                continue;
            }

            $uses[] = $resolver->getImport($use);
        }

        natsort($uses);

        foreach ($uses as $u) {
            $writer->writeln(sprintf('use %s;', $u));
        }
    }

    /**
     * getImports
     *
     * @return array
     */
    protected function getImports()
    {
        return $this->uses;
    }

    /**
     * getObjectDeclarationExtension
     *
     * @return null|string
     */
    protected function getObjectDeclarationExtension()
    {
        return null;
    }

    /**
     * getObjectBody
     *
     * @param Writer $writer
     *
     * @return Writer
     */
    protected function writeObjectBody(Writer $writer)
    {
        if (empty($this->methods)) {
            return $writer;
        }

        if ($this->hasItemsBeforeMethods()) {
            $writer->newline();
        }

        foreach ($this->methods as $method) {
            $writer->writeln($method)->newline();
        }

        $writer->popln();

        return $writer;
    }

    /**
     * hasItemsBeforeMethods
     *
     * @return boolean
     */
    protected function hasItemsBeforeMethods()
    {
        return false;
    }

    /**
     * getAutoGenerateTag
     *
     * @return string
     */
    private function getAutoGenerateTag()
    {
        return sprintf('This file was generated at %s.', (new \DateTime())->format('Y-m-d h:m:s'));
    }

    /**
     * Get the knowen FQN from a name.
     *
     * @param string $name
     *
     * @return array indexed array with two items, $namespace, and $name.
     */
    final protected function desectName($name)
    {
        if (0 === substr_count($name, self::N_SEP) || 0 === strrpos($name, self::N_SEP)) {
            return [null, $name];
        }

        $namespace = substr($name, 0, $pos = strrpos($name, self::N_SEP));
        $name      = substr($name, $pos + 1);

        return [static::trimNs($namespace), $name];
    }

    /**
     * isLocked
     *
     * @return boolean
     */
    final protected function isLocked()
    {
        return (boolean)$this->locked;
    }

    /**
     * lock
     *
     * @return void
     */
    final protected function lock()
    {
        $this->locked = true;
    }

    protected function inNamespace($name)
    {
        list ($ns, $name) = $this->desectName($name);

        if (0 === strcmp($this->getNamespace(), static::trimNs($ns))) {
            return true;
        }

        return false;
    }

    /**
     * setNamespace
     *
     * @param string|null $namespace
     *
     * @return void
     */
    private function setNamespace($namespace = null)
    {
        if (null === $namespace) {
            return;
        }

        $this->namespace = static::trimNs($namespace);
    }

    /**
     * setName
     *
     * @param mixed $name
     *
     * @return void
     */
    private function setName($name)
    {
        list ($ns, $name) = $this->desectName($name);

        $namespace = $this->getNamespace();

        if (null !== $ns) {
            if (null !== $namespace) {
                throw new \InvalidArgumentException();
            } else {
                $this->setNamespace($ns);
            }
        }

        $this->fqn = self::N_SEP.static::trimNs($this->namespace ? $this->namespace.self::N_SEP.$name : $name);
        $this->name = $name;
    }
}
