<?php 

use Mockery as m;
use Illuminate\Config\Repository as Config;
use Illuminate\Cache\CacheManager as Cache;

class OptionTest extends TestCase {

use \Way\Tests\ModelHelpers;

protected $op;
protected $mock;



public function setUp()
{
    parent::setUp();
   
   
    
    
    $this->op = new \Weboap\Option\Option;

    
   
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
	$this->op->set( [ 1 =>'bar' ]);
	$this->op->set( [true =>'bar'] );
	$this->op->set('1.2.3.4.5.6.7.8.', 'f');
}



public function testSet()
{
	$this->op->set( ['foo' => 'bar']);
	$this->op->set( ['foo1' => 'bar1']);
	
	$this->assertTrue( $this->op->has('foo'));
	$this->assertEquals( 'bar', $this->op->get('foo'));
	$this->assertEquals( 'bar1', $this->op->get('foo1'));
	
	$this->op->set( ['option1'=> 'value1', 'option2' => 'value2']);
	$this->assertTrue( $this->op->has('option1'));
	
	
}


public function testForget()
{
	$this->op->set( ['foo' => 'bar']);
	$this->op->forget( 'foo' );
	$this->assertFalse( $this->op->has('foo'));
	
	$this->op->set( ['option1'=> 'value1', 'option2' => 'value2']);
	$this->op->forget( 'option1' );
	$this->assertTrue( $this->op->has('option2'));
	
}


public function testUnicode()
{
    $this->op->set(['a'=>'Hälfte']);
    $this->op->set(['b'=>'Höfe']);
    $this->op->set(['c' => 'Hüfte']);
    $this->op->set(['d' => 'saß']);
    $this->assertEquals('Hälfte', $this->op->get('a'));
    $this->assertEquals('Höfe', $this->op->get('b'));
    $this->assertEquals('Hüfte', $this->op->get('c'));
    $this->assertEquals('saß', $this->op->get('d'));
} 
 
 
/**
* @expectedException InvalidArgumentException
*/
public function testGetInvalidArguments()
{
	$this->op->get([]);
	$this->op->get();
	$this->op->get( 1 );
	$this->op->get( ['foo' =>'bar'] );
	$this->op->get('12.', 'f');
} 
 

public function testResult()
{
	//$this->op['foo'] = 'bar1';
	
	Option::shouldReceive('get')
		->once()
		->with('foo11')
		->andReturn('bar');
		
	$option = Option::get('foo11');
	
	$this->assertEquals('bar', $option);
	
	
	
}




}