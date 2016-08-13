<?php
	use Adepto\SniffArray\Exception\InvalidArrayFormatException;
	use Adepto\SniffArray\Sniff\ArraySniffer;

	class ArraySnifferTest extends PHPUnit_Framework_TestCase {
		public function testSpecConformityPositive() {
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'			=>	'string',
				'otherKey'		=>	'number',
				'yetAnotherKey'	=>	'int'
			], [
				'key'			=>	'someValue',
				'otherKey'		=>	INF,
				'yetAnotherKey'	=>	123
			]));
		}

		public function testSpecConformityNegative() {
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key'			=>	'string',
				'otherKey'		=>	'number',
				'yetAnotherKey'	=>	'int'
			], [
				'key'			=>	'someValue',
				'otherKey'		=>	true,
				'yetAnotherKey'	=>	NAN
			]));
		}

		/**
		 * @expectedException Adepto\SniffArray\Exception\InvalidArrayFormatException
		 */
		public function testThrowOnFailure() {
			$conforms = ArraySniffer::arrayConformsTo([], [], true);
			$this->assertTrue($conforms);

			ArraySniffer::arrayConformsTo([
				'someKey'	=>	'int'
			], [
				'someKey'	=>	'someWrongValue'
			], true);
		}

		public function testNestedConformity() {
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'		=>	'string',
				'nested'	=>	[
					'first'		=>	'int',
					'second'	=>	'bool'
				]
			], [
				'key'		=>	'value',
				'nested'	=>	[
					'first'		=>	-456,
					'second'	=>	false
				]
			]));
		}

		public function testRegExpSpecConformity() {
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key*'		=>	'int',
				'nested*'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], [
				'key'		=>	[123, 456, 789],
				'nested'	=>	[
					[
						'first'		=>	'yo',
						'second'	=>	true
					], [
						'first'		=>	'ho',
						'second'	=>	false
					]
				]
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key*'		=>	'int',
				'nested*'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], [
				'key'		=>	123,
				'nested'	=>	[
					[
						'first'		=>	'yo',
						'second'	=>	true
					], [
						'first'		=>	'ho',
						'second'	=>	false
					]
				]
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key*'		=>	'int',
				'nested*'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], [
				'key'		=>	[],
				'nested'	=>	[
					[
						'first'		=>	'yo',
						'second'	=>	true
					], [
						'first'		=>	'ho',
						'second'	=>	false
					]
				]
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key+'		=>	'int',
				'nested'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], [
				'key'		=>	[123, 456, 789],
				'nested'	=>	[
					'first'		=>	'yo',
					'second'	=>	true
				]
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'		=>	'int',
				'nested+'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], [
				'key'		=>	123,
				'nested'	=>	[
					'first'		=>	'yo',
					'second'	=>	true
				]
			]));

			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key+'		=>	'int',
				'nested'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], [
				'key'		=>	[],
				'nested'	=>	[
					'first'		=>	'yo',
					'second'	=>	true
				]
			]));
		}

		public function testSpecAlternativesConformity() {
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'		=>	'string|bool',
				'otherKey'	=>	'number|null'
			], [
				'key'		=>	'yes',
				'otherKey'	=>	null
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'		=>	'string|bool',
				'otherKey'	=>	'number|null'
			], [
				'key'		=>	true,
				'otherKey'	=>	0
			]));

			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key'		=>	'string|bool',
				'otherKey'	=>	'number|null'
			], [
				'key'		=>	null,
				'otherKey'	=>	false
			]));
		}

		public function testSpecNullableConformity() {
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'			=>	'string',
				'explicitNull'	=>	'bool?'
			], [
				'key'			=>	'value',
				'explicitNull'	=>	null
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'			=>	'string',
				'implicitNull'	=>	'bool?'
			], [
				'key'			=>	'value'
			]));

			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key'			=>	'string',
				'notNullKey'	=>	'number'
			], [
				'key'			=>	'abc',
				'notNullKey'	=>	null
			]));
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key'			=>	'string',
				'notNullKey'	=>	'number'
			], [
				'key'			=>	'abc'
			]));
		}

		public function testSpecStrictConformity() {
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'		=>	'string!',
				'looseKey'	=>	'string',
				'numberKey'	=>	'number!',
				'nanKey'	=>	'number'
			], [
				'key'		=>	'value',
				'looseKey'	=>	'',
				'numberKey'	=>	3.141592,
				'nanKey'	=>	NAN
			]));

			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key'		=>	'string!',
				'looseKey'	=>	'string',
				'numberKey'	=>	'number!',
				'nanKey'	=>	'number'
			], [
				'key'		=>	'',
				'looseKey'	=>	'val',
				'numberKey'	=>	NAN,
				'nanKey'	=>	1.414
			]));
		}
	}
