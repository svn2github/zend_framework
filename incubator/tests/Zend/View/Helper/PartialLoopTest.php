<?php
// Call Zend_View_Helper_PartialLoopTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    $base = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
    $paths = array(
        $base . '/incubator/tests',
        $base . '/incubator/library',
        $base . '/library'
    );
    set_include_path(implode(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_PartialLoopTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_PartialLoop */
require_once 'Zend/View/Helper/PartialLoop.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Controller_Front */
require_once 'Zend/Controller/Front.php';

/**
 * Test class for Zend_View_Helper_PartialLoop.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_PartialLoopTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_PartialLoop
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_PartialLoopTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_PartialLoop();
        Zend_Controller_Front::getInstance()->resetInstance();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    /**
     * @return void
     */
    public function testPartialLoopIteratesOverArray()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', $data);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopIteratesOverIterator()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );
        $o = new Zend_View_Helper_PartialLoop_IteratorTest($data);

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', $o);
        foreach ($data as $item) {
            $string = 'This is an iteration: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopThrowsExceptionWithBadIterator()
    {
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );
        $o = new Zend_View_Helper_PartialLoop_BogusIteratorTest($data);

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        try {
            $result = $this->helper->partialLoop('partialLoop.phtml', $o);
            $this->fail('PartialLoop should only work with arrays and iterators');
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testPartialLoopFindsModule()
    {
        Zend_Controller_Front::getInstance()->addModuleDirectory($this->basePath);
        $data = array(
            array('message' => 'foo'),
            array('message' => 'bar'),
            array('message' => 'baz'),
            array('message' => 'bat')
        );

        $view = new Zend_View(array(
            'scriptPath' => $this->basePath . '/default/views/scripts'
        ));
        $this->helper->setView($view);

        $result = $this->helper->partialLoop('partialLoop.phtml', 'foo', $data);
        foreach ($data as $item) {
            $string = 'This is an iteration in the foo module: ' . $item['message'];
            $this->assertContains($string, $result);
        }
    }
}

class Zend_View_Helper_PartialLoop_IteratorTest implements Iterator
{
    public $items;

    public function __construct(array $array)
    {
        $this->items = $array;
    }

    public function current()
    {
        return current($this->items);
    }

    public function key()
    {
        return key($this->items);
    }

    public function next()
    {
        return next($this->items);
    }

    public function rewind()
    {
        return reset($this->items);
    }

    public function valid()
    {
        return (current($this->items) !== false);
    }
}

class Zend_View_Helper_PartialLoop_BogusIteratorTest
{
}

// Call Zend_View_Helper_PartialLoopTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_PartialLoopTest::main") {
    Zend_View_Helper_PartialLoopTest::main();
}
?>
