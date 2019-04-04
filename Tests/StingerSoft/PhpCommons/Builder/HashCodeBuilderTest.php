<?php
/*
  * This file is part of the Stinger PHP-Commons package.
 *
 * (c) Oliver Kotte <oliver.kotte@stinger-soft.net>
 * (c) Florian Meyer <florian.meyer@stinger-soft.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StingerSoft\PhpCommons\Builder;

use StingerSoft\PhpCommons\String\Utils;

class HashCodeBuilderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage HashCodeBuilder requires a non zero initial value
	 */
	public function testConstructorExZeroFirst() {
		new HashCodeBuilder(0, 0);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage HashCodeBuilder requires an odd initial value
	 */
	public function testConstructorExEvenFirst() {
		new HashCodeBuilder(2, 3);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage HashCodeBuilder requires a non zero multiplier
	 */
	public function testConstructorExZeroSecond() {
		new HashCodeBuilder(3, 0);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage HashCodeBuilder requires an odd multiplier
	 */
	public function testConstructorExEvenSecond() {
		new HashCodeBuilder(3, 2);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage HashCodeBuilder requires an odd initial value
	 */
	public function testConstructorExEvenNegative() {
		new HashCodeBuilder(-2, -2);
	}

	public function testReflectionHashCode() {
		$this->assertEquals(17 * 37, HashCodeBuilder::reflectionHashCode(new TestObject((int)0)));
		$this->assertEquals(17 * 37 + 123456, HashCodeBuilder::reflectionHashCode(new TestObject((int)123456)));
	}

	public function testReflectionHierarchyHashCode() {
		$this->assertEquals((17 * 37) * 37, HashCodeBuilder::reflectionHashCode(new TestSubObject((int)0, (int)0)));
		$this->assertEquals((17 * 37 + 7890) * 37, HashCodeBuilder::reflectionHashCode(new TestSubObject((int)7890, (int)0)));
		$this->assertEquals((17 * 37 + 7890) * 37 + 123456, HashCodeBuilder::reflectionHashCode(new TestSubObject((int)7890, (int)123456)));
	}

	public function testObject() {
		$obj = null;
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append($obj)->toHashCode());
		$obj = new \stdClass();
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 17, $builder->append($obj)->toHashCode());
	}

	public function testObjectArray() {
		$obj = array(null);
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append($obj)->toHashCode());
		$obj = array(new \stdClass(), new \stdClass());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + 17) * 37 + (17 * 37 + 17), $builder->append($obj)->toHashCode());
	}

	public function testObjectWithoutReflection() {
		$obj = null;
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->appendObject($obj, false)->toHashCode());
		$obj = new \stdClass();
		$splHash = Utils::hashCode(spl_object_hash($obj));
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + $splHash, $builder->appendObject($obj, false)->toHashCode());
	}

	public function testObjectArrayWithoutReflection() {
		$obj = array(null);
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->appendObject($obj, false)->toHashCode());
		$obj = array(new \stdClass(), new \stdClass());
		$splHash[] = Utils::hashCode(spl_object_hash($obj[0]));
		$splHash[] = Utils::hashCode(spl_object_hash($obj[1]));
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + $splHash[0]) * 37 + $splHash[1], $builder->appendObject($obj, false)->toHashCode());

		$obj = array(new \stdClass(), 12);
		$splHash = Utils::hashCode(spl_object_hash($obj[0]));
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + $splHash) * 37 + 12, $builder->appendObject($obj, false)->toHashCode());
	}

	public function testObjectWithHashCode() {
		$obj = new TestObjectWithHashCodeMethod();
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 12, $builder->append($obj)->toHashCode());
	}

	public function testObjectArrayWithHashCode() {
		$obj = array(new TestObjectWithHashCodeMethod(), new TestObjectWithHashCodeMethod());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + 12) * 37 + 12, $builder->append($obj)->toHashCode());
	}

	public function testInt() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append((int)0)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 123456, $builder->append((int)123456)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 5, $builder->append((int)5.5)->toHashCode());
	}

	public function testIntArray() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + 0) * 37 + 2, $builder->append(array((int)0, (int)2))->toHashCode());
	}

	public function testBool() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 1, $builder->append(false)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 0, $builder->append(true)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 1, $builder->append((bool)0)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 0, $builder->append((bool)1)->toHashCode());
	}

	public function testBoolArray() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + 1) * 37 + 0, $builder->append(array(false, true))->toHashCode());
	}

	public function testFloat() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append((float)0.0)->toHashCode());
		$float = 2.3;
		$builder = new HashCodeBuilder(17, 37);
		$intVal = unpack('i', pack('f', $float))[1];
		$this->assertEquals(17 * 37 + $intVal, $builder->append($float)->toHashCode());
	}

	public function testFloatArray() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append(array((float)0.0))->toHashCode());
		$floats = array(2.3, 1.0);
		$builder = new HashCodeBuilder(17, 37);
		$intVal[0] = unpack('i', pack('f', $floats[0]))[1];
		$intVal[1] = unpack('i', pack('f', $floats[1]))[1];
		$this->assertEquals((17 * 37 + $intVal[0]) * 37 + $intVal[1], $builder->append($floats)->toHashCode());
	}

	public function testDouble() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append((double)0.0)->toHashCode());
		$double = (double)2.3;
		$builder = new HashCodeBuilder(17, 37);
		$intVal = unpack('i', pack('f', $double))[1];
		$this->assertEquals(17 * 37 + $intVal, $builder->append($double)->toHashCode());
	}

	public function testString() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append('')->toHashCode());
		$string = 'Hello World';
		$builder = new HashCodeBuilder(17, 37);
		$intVal = Utils::hashCode($string);
		$this->assertEquals(17 * 37 + $intVal, $builder->append($string)->toHashCode());
	}

	public function testStringArray() {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append(array(''))->toHashCode());
		$strings[] = 'Hello World';
		$strings[] = 'Dummy';
		$builder = new HashCodeBuilder(17, 37);
		$intVal[] = Utils::hashCode($strings[0]);
		$intVal[] = Utils::hashCode($strings[1]);
		$this->assertEquals((17 * 37 + $intVal[0]) * 37 + $intVal[1], $builder->append($strings)->toHashCode());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage The object to build a hash code for must not be null
	 */
	public function testNullObjectWithReflectionAppend() {
		HashCodeBuilder::reflectionHashCode(null);
	}

	public function testAlreadyRegisteredObjectSkipping() {
		$a = new CyclicTestObject();
		$b = new CyclicTestObject();
		$a->setObject($b);
		$b->setObject($a);

		$this->assertEquals((17 * 37 + 17) * 37 + (17 * 37 + 17), HashCodeBuilder::reflectionHashCode($a));
	}
}

class TestObjectWithHashCodeMethod {

	public function getHashCode() {
		return 12;
	}
}

class CyclicTestObject {

	private $object;

	/**
	 * @return mixed
	 */
	public function getObject() {
		return $this->object;
	}

	/**
	 * @param mixed $object
	 * @return CyclicTestObject
	 */
	public function setObject($object) {
		$this->object = $object;
		return $this;
	}
}

class TestObject {
	private $a;

	public function __construct($a) {
		$this->a = $a;
	}

	/**
	 * @return int
	 */
	public function getA() {
		return $this->a;
	}

	/**
	 * @param int $a
	 * @return TestObject
	 */
	public function setA($a) {
		$this->a = $a;
		return $this;
	}
}

class TestSubObject extends TestObject {

	private $b;

	public function __construct($b, $a = 0) {
		parent::__construct($a);
		$this->b = $b;
	}

	/**
	 * @return int
	 */
	public function getB() {
		return $this->b;
	}

	/**
	 * @param int $b
	 * @return TestSubObject
	 */
	public function setB($b) {
		$this->b = $b;
		return $this;
	}

}