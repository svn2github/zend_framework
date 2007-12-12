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

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Helper for setting and retrieving the doctype
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
class Zend_View_Helper_Doctype
{
    /**#@+
     * DocType constants
     */
    const XHTML1_STRICT       = 'XHTML1_STRICT';
    const XHTML1_TRANSITIONAL = 'XHTML1_TRANSITIONAL';
    const XHTML1_FRAMESET     = 'XHTML1_FRAMESET';
    const HTML4_STRICT        = 'HTML4_STRICT';
    const HTML4_LOOSE         = 'HTML4_LOOSE';
    const HTML4_FRAMESET      = 'HTML4_FRAMESET';
    const CUSTOM_XHTML        = 'CUSTOM_XHTML';
    const CUSTOM              = 'CUSTOM';
    /**#@-*/
    
    /**
     * Default DocType
     * @var string
     */
    protected $_defaultDoctype = self::HTML4_LOOSE;

    /**
     * Registry containing current doctype and mappings
     * @var Zend_Registry
     */
    protected $_registry;

    /**
     * Constructor
     *
     * Map constants to doctype strings, and set default doctype
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_registry = Zend_Registry::getInstance();
        if (!isset($this->_registry[__CLASS__])) {
            $this->_registry[__CLASS__] = array();
            $this->_setDoctype($this->_defaultDoctype);
            $this->_registry[__CLASS__]['doctypes'] = array(
                self::XHTML1_STRICT       => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
                self::XHTML1_TRANSITIONAL => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
                self::XHTML1_FRAMESET     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
                self::HTML4_STRICT        => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
                self::HTML4_LOOSE         => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
                self::HTML4_FRAMESET      => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
            );
        }
    }
    
    /**
     * Set or retrieve doctype
     * 
     * @param  string $doctype 
     * @return Zend_View_Helper_Doctype
     */
    public function doctype($doctype = null)
    {
        if (null !== $doctype) {
            switch ($doctype) {
                case self::XHTML1_STRICT:
                case self::XHTML1_TRANSITIONAL:
                case self::XHTML1_FRAMESET:
                case self::HTML4_STRICT:
                case self::HTML4_LOOSE:
                case self::HTML4_FRAMESET:
                    $this->_setDoctype($doctype);
                    break;
                default:
                    if (substr($doctype, 0, 9) != '<!DOCTYPE') {
                        require_once 'Zend/View/Exception.php';
                        throw new Zend_View_Exception('The specified doctype is malformed');
                    }
                    if (stristr($doctype, 'xhtml')) {
                        $type = self::CUSTOM_XHTML;
                    } else {
                        $type = self::CUSTOM;
                    }
                    $this->_setDoctype($type);
                    $this->_registry[__CLASS__]['doctypes'][$type] = $doctype;
                    break;
            }
        }

        return $this;
    }

    public function _setDoctype($doctype)
    {
        $this->_registry[__CLASS__]['doctype'] = $doctype;
    }
    
    /**
     * Retrieve doctype
     * 
     * @return string
     */
    public function getDoctype()
    {
        return $this->_registry[__CLASS__]['doctype'];
    }

    /**
     * Get doctype => string mappings
     * 
     * @return array
     */
    public function getDoctypes()
    {
        return $this->_registry[__CLASS__]['doctypes'];
    }
    
    /**
     * Is doctype XHTML?
     * 
     * @return boolean
     */
    public function isXhtml()
    {
        return (stristr($this->getDoctype(), 'xhtml') ? true : false);
    }

    /**
     * String representation of doctype
     * 
     * @return string
     */
    public function __toString()
    {
        $doctypes = $this->getDoctypes();
        return $doctypes[$this->getDoctype()];
    }
}
