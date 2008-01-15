<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Xhtml.php';

/**
 * Base class for multi-option form elements
 * 
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
abstract class Zend_Form_Element_Multi extends Zend_Form_Element_Xhtml
{
    /**
     * Array of options for multi-item
     * @var array
     */
    protected $_multiOptions = array();

    /**
     * Separator to use between options; defaults to '<br />'.
     * @var string
     */
    protected $_separator = '<br />';

    /**
     * Retrieve separator
     *
     * @return mixed
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Set separator
     *
     * @param mixed $separator
     * @return self
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    public function getMultiOptions()
    {
        return $this->_multiOptions;
    }

    /**
     * Add many options at once
     * 
     * @param  array $options 
     * @return Zend_Form_Element_Multi
     */
    public function addMultiOptions(array $options)
    {
        foreach ($options as $option) {
            $this->addMultiOption($option);
        }
        return $this;
    }

    /**
     * Set all options at once (overwrites)
     *
     * @param  array $options
     * @return Zend_Form_Element_Multi
     */
    public function setMultiOptions(array $options)
    {
        $this->clearMultiOptions();
        return $this->addMultiOptions($options);
    }

    /**
     * Add an option
     * 
     * @param  mixed $option 
     * @return Zend_Form_Element_Multi
     */
    public function addMultiOption($option)
    {
        $this->_multiOptions[] = $option;
        $decorator = $this->getDecorator('viewHelper');
        if ($decorator) {
            $options = $decorator->getOptions();
            $options['options'] = $this->getMultiOptions();
            $decorator->setOptions($options);
        }
        return $this;
    }

    /**
     * Clear all options
     * 
     * @return Zend_Form_Element_Multi
     */
    public function clearMultiOptions()
    {
        $this->_multiOptions = array();
        return $this;
    }
}
