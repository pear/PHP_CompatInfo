<?php
/**
 * Test tokens that appeared in PHP 5
 *    T_ABSTRACT
 *    T_CATCH
 *    T_FINAL
 *    T_INSTANCEOF
 *    T_PRIVATE
 *    T_PROTECTED
 *    T_PUBLIC
 *    T_THROW
 *    T_TRY
 *    T_CLONE
 *    T_INTERFACE
 *    T_IMPLEMENTS
 *
 * @version    $Id$
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    PHP_CompatInfo
 * @access     public
 * @ignore
 */

require_once 'PHP/CompatInfo.php';

abstract class AbstractClass
{
    abstract protected function getValue();
}

interface iTemplate
{
    public function setVariable($name, $var);
    public function getHtml($template);
}
class Template implements iTemplate
{
    private $vars = array();

    public function setVariable($name, $var)
    {
        $this->vars[$name] = $var;
    }

    public function getHtml($template)
    {
        foreach($this->vars as $name => $value) {
            $template = str_replace('{' . $name . '}', $value, $template);
        }
        return $template;
    }
}



class BaseClass
{
    public $objet1;
    public $objet2;

    public function __construct()
    {
    }

    public function __clone()
    {
        $this->object1 = clone($this->object1);
    }

    private function foo()
    {
    }

    protected function bar()
    {
        if ($this->object1 instanceof BaseClass) {
            return;
        }

        try {
            $error = 'my error';
            throw new Exception($error);

        } catch(Exception $__bar_exception) {

        }
    }

    final public function moreTesting()
    {
        echo "BaseClass::moreTesting() called \n";
    }
}

$info = new PHP_CompatInfo();

$file = __FILE__;
$options = array('debug' => true);

var_dump($info->parseFile($file, $options));
?>