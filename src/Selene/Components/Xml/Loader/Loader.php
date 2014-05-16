<?php

/**
 * This File is part of the Selene\Compiler package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml\Loader;

use \Selene\Components\Xml\Dom\DOMDocument;
use \Selene\Components\Xml\SimpleXMLElement;
use \Selene\Components\Common\Traits\Getter;

/**
 * @class Loader implements LoaderInterface
 * @see LoaderInterface
 *
 * @package Selene\Components\Xml\Loader
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Loader implements LoaderInterface
{
    use Getter;

    /**
     * options
     *
     * @var array
     */
    protected $options;

    /**
     * defaultOptions
     *
     * @var array
     */
    protected $defaultOptions;

    /**
     * errors
     *
     * @var array
     */
    protected $errors;

    /**
     * xmlErrors
     *
     * @var array
     */
    protected $xmlErrors;

    /**
     * @access public
     */
    public function __construct()
    {
        $this->options = [];
        $this->defaultOptions = [];
        $this->errors = [];
        $this->xmlErrors = [];
    }

    /**
     * __clone
     *
     * @access public
     * @return mixed
     */
    public function __clone()
    {
        $this->options = [];
        $this->defaultOptions = [];
        $this->errors = [];
        $this->xmlErrors = [];
    }

    /**
     * setOption
     *
     * @param mixed $option
     * @param mixed $value
     * @access public
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * getOption
     *
     * @param mixed $option
     * @param mixed $default
     * @access public
     * @return mixed
     */
    public function getOption($option = null, $default = null)
    {
        return $this->getDefault($this->options, $option, $default);
    }

    /**
     * load
     *
     * @param mixed $file
     * @access public
     * @return DOMDocument or SimpleXMLElement
     */
    public function load($file, array $options = [])
    {
        $this->loadOptions($options);

        $xml = $this->doLoad($file);

        if ($errors = $this->getErrors()) {
            throw new \Exception($this->formatErrors($errors, $file));
        }

        $this->resotereOptions();

        return $xml;
    }

    /**
     * formatErrors
     *
     * @param array $errors
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function formatErrors(array $errors, $file)
    {
        $output = "[file] $file \n";

        foreach ($errors as $errnum => $error) {
            $output .= "[$errnum] $error \n";
        }

        return $output;
    }

    /**
     * load
     *
     * @param mixed $file
     * @access public
     * @return DOMDocument|SimpleXMLElement
     */
    protected function doLoad($file)
    {
        $domClass = $this->getOption('dom_class', '\Selene\Components\Xml\Dom\DOMDocument');

        $dom = new $domClass('1.0', 'UTF-8');

        $load = ($fromString = $this->getOption('from_string', false)) ? 'loadXML' : 'load';

        if (!$this->loadXmlInDom($dom, $file, $load)) {
            return false;
        }

        if ($simpleXml = $this->getOption('simplexml', false)) {
            $xml = simplexml_import_dom($dom, $this->getOption('simplexml_class', __NAMESPACE__.'\\SimpleXmlElement'));
            return $xml;
        }

        return $dom;
    }

    /**
     * getErrors
     *
     * @access public
     * @return mixed|bool|array
     */
    public function getErrors()
    {
        return $this->getAllErrors();
    }

    /**
     * loadXmlInDom
     *
     * @param \DOMDocument $dom
     * @param mixed $file
     * @param string $load
     * @access protected
     * @return DOMDocument;
     */
    protected function loadXmlInDom(\DOMDocument $dom, $file, $load = 'load')
    {
        $errored = false;

        $usedInternalErrors = libxml_use_internal_errors(true);
        $externalEntitiesDisabled = libxml_disable_entity_loader(false);
        libxml_clear_errors();

        set_error_handler([$this, 'handleXMLErrors']);

        // set LIBXML_NONET to prevent local and remote file inclusion attacks.
        try {
            call_user_func_array(
                [$dom, $load],
                [$file, LIBXML_NONET | LIBXML_DTDATTR | defined('LIBXML_COMPACT') ? LIBXML_COMPACT : 0]
            );
        } catch (\Exception $e) {
            $this->errors[] = trim($e->getMessage(), "\n");
            return false;
        }

        restore_error_handler();

        if ($errors = libxml_get_errors()) {
            $this->xmlErrors = $errors;
            $errored = true;
        }

        // restore previous libxml setting:
        libxml_use_internal_errors($usedInternalErrors);
        libxml_disable_entity_loader($externalEntitiesDisabled);

        if ($errored) {
            return false;
        }

        $dom->normalizeDocument();

        return $dom;
    }

    /**
     * handleXMLErrors
     *
     * @param mixed $errorno
     * @param mixed $errstr
     * @access public
     * @return mixed
     */
    public function handleXMLErrors($errorno, $errstr)
    {
        $this->xmlErrors = libxml_get_errors();

        if (0 === error_reporting()) {
            return false;
        }

        $this->errors[] = trim($errstr, "\n");
    }

    /**
     * getXmlErrors
     *
     * @access private
     * @return mixed
     */
    private function getAllErrors()
    {
        $errors = [];

        foreach ($this->xmlErrors as $error) {
            $errors[] = trim($error->message, "\n");
        }

        $errors = array_merge($this->errors, $errors);
        return empty($errors) ? false : $errors;
    }

    /**
     * loadOptions
     *
     * @param array $options
     *
     * @access private
     * @return mixed
     */
    private function loadOptions(array $options)
    {
        $options = array_merge($this->options, $options);

        foreach ($options as $option => $value) {
            if ($default = $this->getOption($option)) {
                $this->defaultOptions[$option] = $default;
            }

            $this->setOption($option, $value);
        }
    }

    /**
     * resotereOptions
     *
     * @access private
     * @return void
     */
    private function resotereOptions()
    {
        $this->options = $this->defaultOptions;
        $this->defaultOptions = [];
    }
}
