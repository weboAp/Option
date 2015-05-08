<?php 

use Mockery as m;
use Illuminate\Config\Repository as Config;
use Illuminate\Cache\CacheManager as Cache;

class OptionTest extends \Illuminate\Foundation\Testing\TestCase
{

    use \Way\Tests\ModelHelpers;

    protected $op;

    protected $mock;
    
    
    public function createApplication()
    {
                putenv('DB_DEFAULT=sqlite_testing');
                
                require __DIR__ . '/../../../../vendor/autoload.php';
                
                $app = require __DIR__ . '/../../../../bootstrap/app.php';
    

                return $app;
    
    }
    
    
    

    public function setUp()
    {
        parent::setUp();
        
        $this->app->register('Weboap\Option\OptionServiceProvider');
        
        
        $this->op =  $this->app->make('Weboap\Option\Option');
    }

    public function tearDown()
    {
        m::close();
    }

    public function mock($class)
    {
        $mock = m::mock($class);
        $this->app->instance($class, $mock);
        return $mock;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalidArguments()
    {
        $this->op->set([]);
        $this->op->set(['' => 'bar']);
        $this->op->set([1 => 'bar']);
        $this->op->set([true => 'bar']);
        $this->op->set('1.2.3.4.5.6.7.8.', 'f');
    }

    public function testSet()
    {
        $this->op->set(['foo' => 'bar']);
        $this->op->set(['foo1' => 'bar1']);
        $this->assertTrue($this->op->has('foo'));
        $this->assertEquals('bar', $this->op->get('foo'));
        $this->assertEquals('bar1', $this->op->get('foo1'));
        $this->op->set(['option1' => 'value1', 'option2' => 'value2']);
        $this->assertTrue($this->op->has('option1'));
    }

    public function testForget()
    {
        $this->op->set(['foo' => 'bar']);
        $this->op->forget('foo');
        $this->assertFalse($this->op->has('foo'));
        $this->op->set(['option1' => 'value1', 'option2' => 'value2']);
        $this->op->forget('option1');
        $this->assertTrue($this->op->has('option2'));
    }

    public function testUnicode()
    {
        $this->op->set(['a' => 'H�lfte']);
        $this->op->set(['b' => 'H�fe']);
        $this->op->set(['c' => 'H�fte']);
        $this->op->set(['d' => 'sa�']);
        $this->assertEquals('H�lfte', $this->op->get('a'));
        $this->assertEquals('H�fe', $this->op->get('b'));
        $this->assertEquals('H�fte', $this->op->get('c'));
        $this->assertEquals('sa�', $this->op->get('d'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetInvalidArguments()
    {
        $this->op->get([]);
        $this->op->get();
        $this->op->get(1);
        $this->op->get(['foo' => 'bar']);
        $this->op->get('12.', 'f');
    }

    public function testResult()
    {
        //$this->op['foo'] = 'bar1';
        Option::shouldReceive('get')->once()->with('foo11')->andReturn('bar');
        $option = Option::get('foo11');
        $this->assertEquals('bar', $option);
    }

    public function testAutoPrefix()
    {
        Option::set('foo', 'test');
        $value = Option::get('foo');
        $this->assertEquals('test', $value);
        ////
        Option::set('loki.age', '24');
        $value = Option::get('loki.age');
        $this->assertEquals('24', $value);
    }

    public function testWithoutGroupPrefix()
    {
        ////
        Option::set('admin.name', 'loki');
        Option::set('admin.age', '24');
        $value = Option::get('admin.name');
        $this->assertEquals('loki', $value);
        ////
        $values = Option::getGroup('admin');
        $this->assertEquals('loki', array_get($values, 'admin.name'));
        $this->assertEquals('24', array_get($values, 'admin.age'));
        ////
        $values = Option::getGroup('admin', false);
        $this->assertEquals('loki', array_get($values, 'name'));
        $this->assertEquals('24', array_get($values, 'age'));
    }
}