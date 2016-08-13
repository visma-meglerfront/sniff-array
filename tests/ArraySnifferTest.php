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
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'someKey'	=>	'int'
			], [
				'someKey'	=>	42
			], true));

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
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'		=>	'string',
				'nested'	=>	[
					'first'			=>	'int',
					'second'		=>	'bool',
					'furtherNested'	=>	[
						'hello'	=>	'string'
					]
				]
			], [
				'key'		=>	'value',
				'nested'	=>	[
					'first'			=>	-456,
					'second'		=>	false,
					'furtherNested'	=>	[
						'hello'	=>	'world'
					]
				]
			]));

			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'one'	=>	[
					'two'	=>	[
						'three'	=>	[
							'nestedValue'	=>	'bool'
						]
					]
				]
			], [
				'one'	=>	true
			]));
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'one'	=>	[
					'two'	=>	[
						'three'	=>	[
							'nestedValue'	=>	'bool'
						]
					]
				]
			], [
				'one'	=>	[
					'two'	=>	true
				]
			]));
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'one'	=>	[
					'two'	=>	[
						'three'	=>	[
							'nestedValue'	=>	'bool'
						]
					]
				]
			], [
				'one'	=>	[
					'two'	=>	[
						'three'	=>	[
							'nestedValue'	=>	[
								'some'	=>	'value'
							]
						]
					]
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
				'key'		=>	[123, 456, 789],
				'nested'	=>	[]
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
				'key*'		=>	'int',
				'nested*'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], []));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key*'		=>	'int',
				'nested*'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], [
				'key'	=>	null
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
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key{1,3}'		=>	'int',
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
				'key{1,}'		=>	'int',
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
				'key{,3}'		=>	'int',
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
				'key{,}'		=>	'int',
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
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key{1,3}'		=>	'int',
				'nested*'	=>	[
					'first'		=>	'string',
					'second'	=>	'bool'
				]
			], [
				'key'		=>	[123, 456, 789, 987, 654, 321],
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
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key*'		=>	'int',
				'nested{5,}'	=>	[
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
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key*'		=>	'int',
				'nested{,1}'	=>	[
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
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key*'		=>	'int',
			], [
				'key'		=>	[null],
			]));
			$this->assertFalse(ArraySniffer::arrayConformsTo([
				'key*'		=>	'int',
			], [
				'key'		=>	[null, null, null],
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
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'reallyAnything'	=>	'string|bool|number|int|null'
			], [
				'reallyAnything'	=>	'someString'
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'reallyAnything'	=>	'string|bool|number|int|null'
			], [
				'reallyAnything'	=>	true
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'reallyAnything'	=>	'string|bool|number|int|null'
			], [
				'reallyAnything'	=>	-INF
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'reallyAnything'	=>	'string|bool|number|int|null'
			], [
				'reallyAnything'	=>	123456789
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'reallyAnything'	=>	'string|bool|number|int|null'
			], [
				'reallyAnything'	=>	null
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'reallyAnything'	=>	'string|bool|number|int|null',
			], []));

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
				'explicitNull?'	=>	'bool'
			], [
				'key'			=>	'value',
				'explicitNull'	=>	null
			]));
			$this->assertTrue(ArraySniffer::arrayConformsTo([
				'key'			=>	'string',
				'implicitNull?'	=>	'bool'
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
