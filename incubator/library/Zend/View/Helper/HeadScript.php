<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Placeholder.php 7078 2007-12-11 14:29:33Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container_Standalone */
require_once 'Zend/View/Helper/Placeholder/Container/Standalone.php';

/**
 * Helper for setting and retrieving title element for HTML head
 *
 * @uses       Zend_View_Helper_Placeholder_Container_Standalone
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_HeadScript extends Zend_View_Helper_Placeholder_Container_Standalone
{
    /**#@+
     * Script type contants
     * @const string
     */
    const FILE   = 'FILE';
    const SCRIPT = 'SCRIPT';
    /**#@-*/
    
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Zend_View_Helper_HeadScript';

    /**#@+
     * Capture type and/or attributes (used for hinting during capture)
     * @var string
     */
    protected $_captureScriptType  = null;
    protected $_captureScriptAttrs = null;
    /**#@-*/

    /**
     * Optional allowed attributes for script tag
     * @var array
     */
    protected $_optionalAttributes = array(
        'charset', 'defer', 'language', 'src'
    );

    /**
     * Required attributes for script tag
     * @var string
     */
    protected $_requiredAttributes = array('type');

    /**
     * Whether or not to format scripts using CDATA; used only if doctype 
     * helper is not accessible
     * @var bool
     */
    public $useCdata = false;

    /**
     * Constructor
     *
     * Set separator to PHP_EOL.
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSeparator(PHP_EOL);
    }
    
    /**
     * Return headScript object
     *
     * Returns headScript helper object; optionally, allows specifying 
     *
     * @param  string $mode Script or file
     * @param  string $spec Script/url
     * @param  string $placement Append, prepend, or set
     * @param  string $type Script type and/or array of script attributes
     * @param  array $attrs Array of script attributes
     * @return Zend_View_Helper_HeadScript
     */
    public function headScript($mode = Zend_View_Helper_HeadScript::FILE, $spec = null, $placement = 'APPEND', array $attrs = array(), $type = 'text/javascript')
    {
        if ((null !== $spec) && is_string($spec)) {
            $action    = ucfirst(strtolower($mode));
            $placement = strtolower($placement);
            switch ($placement) {
                case 'set':
                case 'prepend':
                case 'append':
                    $action = $placement . $action;
                    break;
                default:
                    $action = 'append' . $action;
                    break;
            }
            $this->$action($spec, $type, $attrs);
        }

        return $this;
    }
   
    /**
     * Start capture action
     * 
     * @param  mixed $captureType 
     * @param  string $typeOrAttrs 
     * @return void
     */
    public function captureStart($captureType = Zend_View_Helper_Placeholder_Container_Abstract::APPEND, $type = 'text/javascript', $attrs = array())
    {
        $this->_captureType        = $captureType;
        $this->_captureScriptType  = $type;
        $this->_captureScriptAttrs = $attrs;
        return parent::captureStart($captureType);
    }
    
    /**
     * End capture action and store
     * 
     * @return void
     */
    public function captureEnd()
    {
        $content                   = ob_get_clean();
        $type                      = $this->_captureScriptType;
        $attrs                     = $this->_captureScriptAttrs;
        $this->_captureScriptType  = null;
        $this->_captureScriptAttrs = null;
        $this->_captureLock        = false;

        switch ($this->_captureType) {
            case self::SET:
            case self::PREPEND:
            case self::APPEND:
                $action = strtolower($this->_captureType) . 'Script';
                break;
            default:
                $action = 'appendScript';
                break;
        }
        $this->$action($content, $type, $attrs);
    }

    /**
     * Overload method access
     *
     * Allows the following method calls:
     * - appendFile($src, $type = 'text/javascript', $attrs = array())
     * - offsetSetFile($index, $src, $type = 'text/javascript', $attrs = array())
     * - prependFile($src, $type = 'text/javascript', $attrs = array())
     * - setFile($src, $type = 'text/javascript', $attrs = array())
     * - appendScript($script, $type = 'text/javascript', $attrs = array())
     * - offsetSetScript($index, $src, $type = 'text/javascript', $attrs = array())
     * - prependScript($script, $type = 'text/javascript', $attrs = array())
     * - setScript($script, $type = 'text/javascript', $attrs = array())
     * 
     * @param  string $method 
     * @param  array $args 
     * @return Zend_View_Helper_HeadScript
     * @throws Zend_View_Exception if too few arguments or invalid method
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(?P<action>set|(ap|pre)pend|offsetSet)(?P<mode>File|Script)$/', $method, $matches)) {
            if (1 > count($args)) {
                require_once 'Zend/View/Exception.php';
                throw new Zend_View_Exception(sprintf('Method "%s" requires at least one argument', $method));
            }

            $action  = $matches['action'];
            $mode    = strtolower($matches['mode']);
            $type    = 'text/javascript';
            $attrs   = array();

            if ('offsetSet' == $action) {
                $index = array_shift($args);
                if (1 > count($args)) {
                    require_once 'Zend/View/Exception.php';
                    throw new Zend_View_Exception(sprintf('Method "%s" requires at least two arguments, an index and source', $method));
                }
            }

            $content = $args[0];

            if (isset($args[1])) {
                $type = (string) $args[1];
            }
            if (isset($args[2])) {
                $attrs = (array) $args[2];
            }

            switch ($mode) {
                case 'script':
                    $item = $this->createData($type, $attrs, $content);
                    if ('offsetSet' == $action) {
                        $this->offsetSet($index, $item);
                    } else {
                        $this->$action($item);
                    }
                    break;
                case 'file':
                default:
                    $attrs['src'] = $content;
                    $item = $this->createData($type, $attrs);
                    if ('offsetSet' == $action) {
                        $this->offsetSet($index, $item);
                    } else {
                        $this->$action($item);
                    }
                    break;
            }

            return $this;
        }

        require_once 'Zend/View/Exception.php';
        throw new Zend_View_Exception(sprintf('Method "%s" does not exist', $method));
    }

    /**
     * Is the script provided valid?
     * 
     * @param  mixed $value 
     * @param  string $method 
     * @return bool
     */
    protected function _isValid($value)
    {
        if ((!$value instanceof stdClass)
            || !isset($value->type)
            || (!isset($value->source) && !isset($value->attributes)))
        {
            return false;
        }

        return true;
    }

    /**
     * Override append
     * 
     * @param  string $value 
     * @return void
     */
    public function append($value)
    {
        if (!$this->_isValid($value)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('Invalid argument passed to append(); please use one of the helper methods, appendScript() or appendFile()');
        }

        return parent::append($value);
    }

    /**
     * Override prepend
     * 
     * @param  string $value 
     * @return void
     */
    public function prepend($value)
    {
        if (!$this->_isValid($value)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('Invalid argument passed to prepend(); please use one of the helper methods, prependScript() or prependFile()');
        }

        return parent::prepend($value);
    }

    /**
     * Override set
     * 
     * @param  string $value 
     * @return void
     */
    public function set($value)
    {
        if (!$this->_isValid($value)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('Invalid argument passed to set(); please use one of the helper methods, setScript() or setFile()');
        }

        return parent::set($value);
    }

    /**
     * Override offsetSet
     * 
     * @param  string|int $index 
     * @param  mixed $value 
     * @return void
     */
    public function offsetSet($index, $value)
    {
        if (!$this->_isValid($value)) {
            require_once 'Zend/View/Exception.php';
            throw new Zend_View_Exception('Invalid argument passed to offsetSet(); please use one of the helper methods, offsetSetScript() or offsetSetFile()');
        }

        $this->_isValid($value);
        return parent::offsetSet($index, $value);
    }

    /**
     * Create script HTML
     * 
     * @param  string $type 
     * @param  array $attributes 
     * @param  string $content 
     * @param  string|int $indent 
     * @return string
     */
    public function itemToString($item, $indent, $escapeStart, $escapeEnd)
    {
        $attrString = '';
        if (!empty($item->attributes)) {
            foreach ($item->attributes as $key => $value) {
                if (!in_array($key, $this->_optionalAttributes)) {
                    continue;
                }
                if ('defer' == $key) {
                    $value = 'defer';
                }
                $attrString .= sprintf(' %s="%s"', $key, htmlspecialchars($value));
            }
        }

        $html  = '<script type="' . htmlspecialchars($item->type) . '"' . $attrString . '>';
        if (!empty($item->source)) {
              $html .= PHP_EOL . $indent . $escapeStart . PHP_EOL . $indent . $item->source . PHP_EOL . $indent . $escapeEnd . PHP_EOL;
        }
        $html .= '</script>';

        return $html;
    }

    /**
     * Retrieve string representation
     * 
     * @param  string|int $indent 
     * @return string
     */
    public function toString($indent = null)
    {
        if (null !== $indent) {
            if (!is_int($indent) && !is_string($indent)) {
                $indent = $this->_indent;
            }
        } else {
            $indent = $this->_indent;
        }

        if ($this->view) {
            $useCdata = $this->view->doctype()->isXhtml() ? true : false;
        } else {
            $useCdata = $this->useCdata ? true : false;
        }
        $escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
        $escapeEnd   = ($useCdata) ? '//]]>'       : '//-->';

        $items = array();
        foreach ($this as $item) {
            if (!$this->_isValid($item)) {
                continue;
            }

            $items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
        }

        return implode($this->getSeparator(), $items);
    }

    /**
     * Create data item containing all necessary components of script
     * 
     * @param  string $type 
     * @param  array $attributes 
     * @param  string $content 
     * @return stdClass
     */
    public function createData($type, array $attributes, $content = null)
    {
        $data             = new stdClass();
        $data->type       = $type;
        $data->attributes = $attributes;
        $data->source     = $content;
        return $data;
    }
}
