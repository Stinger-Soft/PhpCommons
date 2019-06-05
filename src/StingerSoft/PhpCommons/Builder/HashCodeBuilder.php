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
use ReflectionClass;
use ReflectionException;
use StingerSoft\PhpCommons\String\Utils;
use Traversable;

class HashCodeBuilder {

	private static $debug = false;

	private static $registry = [];

	/** @var int */
	private $iConstant;

	/** @var int */
	private $iTotal;

	public function __construct(int $initialNonZeroOddNumber = 17, int $multiplierNonZeroOddNumber = 37) {
		if($initialNonZeroOddNumber === 0) {
			throw new InvalidArgumentException('HashCodeBuilder requires a non zero initial value');
		}
		if($initialNonZeroOddNumber % 2 === 0) {
			throw new InvalidArgumentException('HashCodeBuilder requires an odd initial value');
		}
		if($multiplierNonZeroOddNumber === 0) {
			throw new InvalidArgumentException('HashCodeBuilder requires a non zero multiplier');
		}
		if($multiplierNonZeroOddNumber % 2 === 0) {
			throw new InvalidArgumentException('HashCodeBuilder requires an odd multiplier');
		}
		$this->iConstant = $multiplierNonZeroOddNumber;
		$this->iTotal = $initialNonZeroOddNumber;
	}

	protected static function isRegistered($object, ReflectionClass $clazz): bool {
		return array_key_exists(spl_object_hash($object) . '-' . $clazz->name, self::$registry);
	}

	protected static function register($object, ReflectionClass $clazz): void {
		self::$registry[spl_object_hash($object) . '-' . $clazz->name] = true;
	}

	/**
	 * @param $debug bool
	 */
	public static function setDebug(bool $debug): void {
		self::$debug = $debug;
	}

	/**
	 * @param object          $object
	 * @param ReflectionClass $clazz
	 * @param HashCodeBuilder $builder
	 * @param string[]        $excludeFields
	 * @throws ReflectionException
	 */
	private static function reflectionAppend($object, ReflectionClass $clazz, HashCodeBuilder $builder, array $excludeFields = []): void {
		if(self::isRegistered($object, $clazz)) {
			self::log("{$clazz->name} is already registered, skipping!" . PHP_EOL);
			return;
		}

		self::register($object, $clazz);
		$fields = $clazz->getProperties();
		self::log("{$clazz->name} has the following fields: [" . implode(array_map(static function ($field) {
				return $field->name;
			}, $fields)) . ']' . PHP_EOL);
		foreach($fields as $field) {
			if(!in_array($field->name, $excludeFields, true) && !$field->isStatic()) {
				$field->setAccessible(true);
				$value = $field->getValue($object);
				$builder->append($value);
			}
		}
	}

	/**
	 * @param             $object
	 * @param int         $initialNonZeroOddNumber
	 * @param int         $multiplierNonZeroOddNumber
	 * @param bool        $includeParents
	 * @param array       $excludeFields
	 * @param string|null $reflectUpToClass
	 * @return int
	 * @throws InvalidArgumentException in case the given object is null
	 * @throws ReflectionException
	 */
	public static function reflectionHashCode($object, int $initialNonZeroOddNumber = 17, int $multiplierNonZeroOddNumber = 37,
											  $includeParents = true, array $excludeFields = [], $reflectUpToClass = null): int {
		if($object === null) {
			throw new InvalidArgumentException('The object to build a hash code for must not be null');
		}
		$clazz = new ReflectionClass($object);
		$builder = new HashCodeBuilder($initialNonZeroOddNumber, $multiplierNonZeroOddNumber);
		return self::_reflectionHashCode($object, $clazz, $builder, $includeParents, $excludeFields,
			$reflectUpToClass);
	}

	/**
	 * @param                 $object
	 * @param ReflectionClass $clazz
	 * @param HashCodeBuilder $builder
	 * @param bool            $includeParents
	 * @param array           $excludeFields
	 * @param string|null     $reflectUpToClass
	 * @return int
	 * @throws ReflectionException
	 */
	private static function _reflectionHashCode($object, ReflectionClass $clazz, HashCodeBuilder $builder, $includeParents = true, array $excludeFields = [], string $reflectUpToClass = null): int {
		self::reflectionAppend($object, $clazz, $builder, $excludeFields);
		if($includeParents) {
			$localClazz = $clazz;
			while(($parentClazz = $localClazz->getParentClass()) !== false && $localClazz->name !== $reflectUpToClass) {
				self::log("traversing parent '{$parentClazz->name}' of '{$localClazz->name}'" . PHP_EOL);
				$localClazz = $parentClazz;
				self::reflectionAppend($object, $localClazz, $builder, $excludeFields);
			}
		}
		self::$registry = [];
		return $builder->toHashCode();
	}

	/**
	 * @param null|array|bool|int|double|float|object|Traversable $value
	 * @return $this
	 * @throws ReflectionException
	 */
	public function append($value = null): self {
		$oldTotal = $this->iTotal;
		if($value === null) {
			$this->appendNull();
		}
		if(is_bool($value)) {
			$this->appendBoolean($value);
		}
		if(is_float($value)) {
			$this->appendFloat($value);
		}
		if(is_int($value)) {
			$this->appendInt($value);
		}
		if(is_string($value)) {
			$this->appendString($value);
		}
		if(is_object($value)) {
			$this->appendObject($value);
		}
		if($value instanceof Traversable || is_array($value)) {
			/** @noinspection ForeachSourceInspection */
			foreach($value as $i => $iValue) {
				$this->append($value[$i]);
			}
		}
		self::log("$oldTotal  -> {$this->iTotal}" . PHP_EOL);
		return $this;
	}

	/**
	 * Append a <code>hashCode</code> for a <code>boolean</code>.
	 * This adds <code>1</code> when true, and <code>0</code> when false to the <code>hashCode</code>.
	 *
	 * @param bool $value the boolean to add to the <code>hashCode</code>
	 * @return $this
	 */
	public function appendBoolean(bool $value): self {
		$this->_append($value === true ? 0 : 1);
		return $this;
	}

	public function appendFloat(float $value) {
		$this->_append(unpack('i', pack('f', $value))[1]);
		return $this;
	}

	public function appendInt(int $value) {
		$this->_append($value);
		return $this;
	}

	public function appendString(string $value) {
		$this->_append(Utils::hashCode($value));
		return $this;
	}

	private function _append(int $value): void {
		$oldTotal = $this->iTotal;
		$this->iTotal = $oldTotal * $this->iConstant + $value;
		self::log("{$this->iTotal} = $oldTotal  * {$this->iConstant} + $value" . PHP_EOL);
	}

	/**
	 * @param object|array $value
	 * @param bool         $useReflection
	 * @return $this
	 * @throws ReflectionException
	 */
	public function appendObject($value, $useReflection = true): self {
		if($value === null) {
			$this->appendNull();
		} else if(is_array($value)) {
			foreach($value as $tmpValue) {
				if(is_object($tmpValue)) {
					$this->appendObject($tmpValue, $useReflection);
				} else {
					$this->append($tmpValue);
				}
			}
		} else {
			$reflectionClass = new ReflectionClass($value);
			if($reflectionClass->hasMethod('getHashCode')) {
				$hashCode = $reflectionClass->getMethod('getHashCode')->invoke($value);
			} else if($useReflection) {
				$hashCode = self::_reflectionHashCode($value, new ReflectionClass($value), $this);
			} else {
				$hashCode = Utils::hashCode(spl_object_hash($value));
			}
			$this->append($hashCode);
		}
		return $this;
	}

	public function appendNull(): self {
		$this->iTotal *= $this->iConstant;
		return $this;
	}

	/**
	 * Return the computed <code>hashCode</code>.
	 *
	 * @return int <code>hashCode</code> based on the fields appended
	 */
	public function toHashCode(): int {
		return $this->iTotal;
	}

	private static function log($message): void {
		if(self::$debug) {
			fwrite(STDERR, $message);
		}
	}
}