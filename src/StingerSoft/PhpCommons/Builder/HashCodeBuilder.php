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

class HashCodeBuilder {

	private static $debug = false;

	private static $registry = array();

	private $iConstant;

	private $iTotal;

	public function __construct($initialNonZeroOddNumber = 17, $multiplierNonZeroOddNumber = 37) {
		if($initialNonZeroOddNumber === 0) {
			throw new \InvalidArgumentException('HashCodeBuilder requires a non zero initial value');
		}
		if($initialNonZeroOddNumber % 2 === 0) {
			throw new \InvalidArgumentException('HashCodeBuilder requires an odd initial value');
		}
		if($multiplierNonZeroOddNumber === 0) {
			throw new \InvalidArgumentException('HashCodeBuilder requires a non zero multiplier');
		}
		if($multiplierNonZeroOddNumber % 2 === 0) {
			throw new \InvalidArgumentException('HashCodeBuilder requires an odd multiplier');
		}
		$this->iConstant = $multiplierNonZeroOddNumber;
		$this->iTotal = $initialNonZeroOddNumber;
	}

	protected static function isRegistered($object, \ReflectionClass $clazz) {
		return array_key_exists(spl_object_hash($object) . '-' . $clazz->name, self::$registry);
	}

	protected static function register($object, \ReflectionClass $clazz) {
		self::$registry[spl_object_hash($object) . '-' . $clazz->name] = true;
	}

	/**
	 * @param $debug bool
	 */
	public static function setDebug($debug) {
		self::$debug = filter_var($debug, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @param object           $object
	 * @param \ReflectionClass $clazz
	 * @param HashCodeBuilder  $builder
	 * @param string[]         $excludeFields
	 */
	private static function reflectionAppend($object, \ReflectionClass $clazz, $builder, array $excludeFields = array()) {
		if(self::isRegistered($object, $clazz)) {
			self::log("{$clazz->name} is already registered, skipping!" . PHP_EOL);
			return;
		}

		self::register($object, $clazz);
		$fields = $clazz->getProperties();
		self::log("{$clazz->name} has the following fields: [" . implode(array_map(function ($field) {
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
	 * @param       $object
	 * @param int   $initialNonZeroOddNumber
	 * @param int   $multiplierNonZeroOddNumber
	 * @param bool  $includeParents
	 * @param array $excludeFields
	 * @param null  $reflectUpToClass
	 * @return int
	 * @throws \InvalidArgumentException in case the given object is null
	 */
	public static function reflectionHashCode($object, $initialNonZeroOddNumber = 17, $multiplierNonZeroOddNumber = 37,
											  $includeParents = true, array $excludeFields = array(), $reflectUpToClass = null) {
		if($object === null) {
			throw new \InvalidArgumentException('The object to build a hash code for must not be null');
		}
		$clazz = new \ReflectionClass($object);
		$builder = new HashCodeBuilder($initialNonZeroOddNumber, $multiplierNonZeroOddNumber);
		return self::_reflectionHashCode($object, $clazz, $builder, $includeParents, $excludeFields,
			$reflectUpToClass);
	}

	private static function _reflectionHashCode($object, \ReflectionClass $clazz, HashCodeBuilder $builder, $includeParents = true, array $excludeFields = array(), $reflectUpToClass = null) {
		self::reflectionAppend($object, $clazz, $builder, $excludeFields);
		if($includeParents) {
			while(($parentClazz = $clazz->getParentClass()) !== false && $clazz->name !== $reflectUpToClass) {
				self::log("traversing parent '{$parentClazz->name}' of '{$clazz->name}'" . PHP_EOL);
				$clazz = $parentClazz;
				self::reflectionAppend($object, $clazz, $builder, $excludeFields);
			}
		}
		self::$registry = array();
		return $builder->toHashCode();
	}

	/**
	 * @param null|array|bool|int|double|float|object|\Traversable $value
	 * @return $this
	 */
	public function append($value = null) {
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
		if($value instanceof \Traversable || is_array($value)) {
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
	public function appendBoolean($value) {
		$this->_append($value === true ? 0 : 1);
		return $this;
	}

	public function appendFloat($value) {
		$this->_append(unpack('i', pack('f', $value))[1]);
		return $this;
	}

	public function appendInt($value) {
		$this->_append($value);
		return $this;
	}

	public function appendString($value) {
		$this->_append(Utils::hashCode($value));
		return $this;
	}

	private function _append($value) {
		$oldTotal = $this->iTotal;
		$this->iTotal = $oldTotal * $this->iConstant + $value;
		self::log("{$this->iTotal} = $oldTotal  * {$this->iConstant} + $value" . PHP_EOL);
	}

	public function appendObject($value, $useReflection = true) {
		if($value === null) {
			$this->appendNull();
		} else {
			if(is_array($value)) {
				foreach($value as $tmpValue) {
					if(is_object($tmpValue)) {
						$this->appendObject($tmpValue, $useReflection);
					} else {
						$this->append($tmpValue);
					}
				}
			} else {
				$reflectionClass = new \ReflectionClass($value);
				if($reflectionClass->hasMethod('getHashCode')) {
					$hashCode = $reflectionClass->getMethod('getHashCode')->invoke($value);
				} else if($useReflection) {
					$hashCode = self::_reflectionHashCode($value, new \ReflectionClass($value), $this);
				} else {
					$hashCode = Utils::hashCode(spl_object_hash($value));
				}
				$this->append($hashCode);
			}
		}
		return $this;
	}

	public function appendNull() {
		$this->iTotal *= $this->iConstant;
	}

	/**
	 * Return the computed <code>hashCode</code>.
	 *
	 * @return int <code>hashCode</code> based on the fields appended
	 */
	public function toHashCode() {
		return $this->iTotal;
	}

	private static function log($message) {
		if(self::$debug) {
			fwrite(STDERR, $message);
		}
	}
}