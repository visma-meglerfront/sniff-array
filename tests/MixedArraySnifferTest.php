<?php
	use Adepto\SniffArray\Sniff\{
		MixedArraySniffer, SplSniffer
	};

	class MixedArraySnifferTest extends PHPUnit_Framework_TestCase {
		/** @var SplSniffer */
		protected $sniffer;

		protected function setUp() {
			$this->sniffer = new MixedArraySniffer();
		}

		public function testStaticCreation() {
			$sniffer = SplSniffer::forType('array');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\MixedArraySniffer', get_class($sniffer));

			$sniffer = SplSniffer::forType('mixedArray');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\MixedArraySniffer', get_class($sniffer));
		}

		public function testSniffPositive() {
			$this->assertTrue($this->sniffer->sniff([]));
			$this->assertTrue($this->sniffer->sniff(['a', 'sequential', 'array']));
			$this->assertTrue($this->sniffer->sniff(['string', true, 123]));
			$this->assertTrue($this->sniffer->sniff([
				'key'	=>	'value',
				'flag'	=>	true,
				'count'	=>	42
			]));
		}

		public function testSniffNegative() {
			$this->assertFalse($this->sniffer->sniff(true));
			$this->assertFalse($this->sniffer->sniff(false));
			$this->assertFalse($this->sniffer->sniff(''));
			$this->assertFalse($this->sniffer->sniff('string'));
			$this->assertFalse($this->sniffer->sniff(null));
			$this->assertFalse($this->sniffer->sniff(123));
			$this->assertFalse($this->sniffer->sniff(123.456));
			$this->assertFalse($this->sniffer->sniff(INF));
			$this->assertFalse($this->sniffer->sniff(NAN));
		}

		public function testSniffStrict() {
			$this->assertTrue($this->sniffer->sniff(['a', 'sequential', 'array'], true));
			$this->assertTrue($this->sniffer->sniff(['string', true, 123], true));
			$this->assertTrue($this->sniffer->sniff([
				'key'	=>	'value',
				'flag'	=>	true,
				'count'	=>	42
			], true));

			$this->assertFalse($this->sniffer->sniff([], true));
		}
	}
