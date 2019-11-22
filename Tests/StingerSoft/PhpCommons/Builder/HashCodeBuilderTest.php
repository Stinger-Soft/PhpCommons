<?php
declare(strict_types=1);

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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use stdClass;
use StingerSoft\PhpCommons\String\Utils;

class HashCodeBuilderTest extends TestCase {

	public function testConstructorExZeroFirst(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('HashCodeBuilder requires a non zero initial value');
		new HashCodeBuilder(0, 0);
	}

	public function testConstructorExEvenFirst(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('HashCodeBuilder requires an odd initial value');
		new HashCodeBuilder(2, 3);
	}

	public function testConstructorExZeroSecond(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('HashCodeBuilder requires a non zero multiplier');
		new HashCodeBuilder(3, 0);
	}

	public function testConstructorExEvenSecond(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('HashCodeBuilder requires an odd multiplier');
		new HashCodeBuilder(3, 2);
	}

	public function testConstructorExEvenNegative(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('HashCodeBuilder requires an odd initial value');
		new HashCodeBuilder(-2, -2);
	}

	/**
	 * @throws ReflectionException
	 */
	public function testReflectionHashCode(): void {
		$this->assertEquals(17 * 37, HashCodeBuilder::reflectionHashCode(new TestObject(0)));
		$this->assertEquals(17 * 37 + 123456, HashCodeBuilder::reflectionHashCode(new TestObject(123456)));
	}

	/**
	 * @throws ReflectionException
	 */
	public function testReflectionHierarchyHashCode(): void {
		$this->assertEquals((17 * 37) * 37, HashCodeBuilder::reflectionHashCode(new TestSubObject(0, 0)));
		$this->assertEquals((17 * 37 + 7890) * 37, HashCodeBuilder::reflectionHashCode(new TestSubObject(7890, 0)));
		$this->assertEquals((17 * 37 + 7890) * 37 + 123456, HashCodeBuilder::reflectionHashCode(new TestSubObject(7890, 123456)));
	}

	/**
	 * @throws ReflectionException
	 */
	public function testObject(): void {
		HashCodeBuilder::setDebug(true);
		$obj = null;
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append($obj)->toHashCode());
		$obj = new stdClass();
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 17, $builder->append($obj)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testObjectArray(): void {
		HashCodeBuilder::setDebug(false);
		$obj = [null];
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append($obj)->toHashCode());
		$obj = [new stdClass(), new stdClass()];
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + 17) * 37 + (17 * 37 + 17), $builder->append($obj)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testObjectWithoutReflection(): void {
		$obj = null;
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->appendObject($obj, false)->toHashCode());
		$obj = new stdClass();
		$splHash = Utils::hashCode(spl_object_hash($obj));
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + $splHash, $builder->appendObject($obj, false)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testObjectArrayWithoutReflection(): void {
		$obj = [null];
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->appendObject($obj, false)->toHashCode());
		$obj = [new stdClass(), new stdClass()];
		$splHash[] = Utils::hashCode(spl_object_hash($obj[0]));
		$splHash[] = Utils::hashCode(spl_object_hash($obj[1]));
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + $splHash[0]) * 37 + $splHash[1], $builder->appendObject($obj, false)->toHashCode());

		$obj = [new stdClass(), 12];
		$splHash = Utils::hashCode(spl_object_hash($obj[0]));
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + $splHash) * 37 + 12, $builder->appendObject($obj, false)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testObjectWithHashCode(): void {
		$obj = new TestObjectWithHashCodeMethod();
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 12, $builder->append($obj)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testObjectArrayWithHashCode(): void {
		$obj = [new TestObjectWithHashCodeMethod(), new TestObjectWithHashCodeMethod()];
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + 12) * 37 + 12, $builder->append($obj)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testInt(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append(0)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 123456, $builder->append(123456)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 5, $builder->append((int)5.5)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testIntArray(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + 0) * 37 + 2, $builder->append([0, 2])->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testBool(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 1, $builder->append(false)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 0, $builder->append(true)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 1, $builder->append((bool)0)->toHashCode());
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37 + 0, $builder->append((bool)1)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testBoolArray(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals((17 * 37 + 1) * 37 + 0, $builder->append([false, true])->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testFloat(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append(0.0)->toHashCode());
		$float = 2.3;
		$builder = new HashCodeBuilder(17, 37);
		$intVal = unpack('i', pack('f', $float))[1];
		$this->assertEquals(17 * 37 + $intVal, $builder->append($float)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testFloatArray(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append([0.0])->toHashCode());
		$floats = [2.3, 1.0];
		$builder = new HashCodeBuilder(17, 37);
		$intVal[0] = unpack('i', pack('f', $floats[0]))[1];
		$intVal[1] = unpack('i', pack('f', $floats[1]))[1];
		$this->assertEquals((17 * 37 + $intVal[0]) * 37 + $intVal[1], $builder->append($floats)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testDouble(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append(0.0)->toHashCode());
		$double = 2.3;
		$builder = new HashCodeBuilder(17, 37);
		$intVal = unpack('i', pack('f', $double))[1];
		$this->assertEquals(17 * 37 + $intVal, $builder->append($double)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testString(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append('')->toHashCode());
		$string = 'Hello World';
		$builder = new HashCodeBuilder(17, 37);
		$intVal = Utils::hashCode($string);
		$this->assertEquals(17 * 37 + $intVal, $builder->append($string)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testStringArray(): void {
		$builder = new HashCodeBuilder(17, 37);
		$this->assertEquals(17 * 37, $builder->append([''])->toHashCode());
		$strings[] = 'Hello World';
		$strings[] = 'Dummy';
		$builder = new HashCodeBuilder(17, 37);
		$intVal[] = Utils::hashCode($strings[0]);
		$intVal[] = Utils::hashCode($strings[1]);
		$this->assertEquals((17 * 37 + $intVal[0]) * 37 + $intVal[1], $builder->append($strings)->toHashCode());
	}

	/**
	 * @throws ReflectionException
	 */
	public function testNullObjectWithReflectionAppend(): void {
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('The object to build a hash code for must not be null');
		HashCodeBuilder::reflectionHashCode(null);
	}

	/**
	 * @throws ReflectionException
	 */
	public function testAlreadyRegisteredObjectSkipping(): void {
		$a = new CyclicTestObject();
		$b = new CyclicTestObject();
		$a->setObject($b);
		$b->setObject($a);

		$this->assertEquals((17 * 37 + 17) * 37 + (17 * 37 + 17), HashCodeBuilder::reflectionHashCode($a));
	}
}

class TestObjectWithHashCodeMethod {

	public function getHashCode(): int {
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
	public function setObject($object): CyclicTestObject {
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
	public function getA(): int {
		return $this->a;
	}

	/**
	 * @param int $a
	 * @return TestObject
	 */
	public function setA($a): TestObject {
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
	public function getB(): int {
		return $this->b;
	}

	/**
	 * @param int $b
	 * @return TestSubObject
	 */
	public function setB($b): TestSubObject {
		$this->b = $b;
		return $this;
	}

}