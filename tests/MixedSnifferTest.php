<?php
	use Adepto\SniffArray\Sniff\{
		MixedSniffer, SplSniffer
	};

	class MixedSnifferTest extends PHPUnit_Framework_TestCase {
		/** @var SplSniffer */
		protected $sniffer;

		protected function setUp() {
			$this->sniffer = SplSniffer::forType('mixed');
		}

		public function testStaticCreation() {
			$sniffer = SplSniffer::forType('mixed');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\MixedSniffer', get_class($sniffer));
		}

		public function testSniffPositive() {
			$this->assertTrue($this->sniffer->sniff(true));
			$this->assertTrue($this->sniffer->sniff(false));
			$this->assertTrue($this->sniffer->sniff(''));
			$this->assertTrue($this->sniffer->sniff('string'));
			$this->assertTrue($this->sniffer->sniff(null));
			$this->assertTrue($this->sniffer->sniff(123));
			$this->assertTrue($this->sniffer->sniff(123.456));
			$this->assertTrue($this->sniffer->sniff(INF));
			$this->assertTrue($this->sniffer->sniff(NAN));
			$this->assertTrue($this->sniffer->sniff([]));
			$this->assertTrue($this->sniffer->sniff(['a', 'sequential', 'array']));
			$this->assertTrue($this->sniffer->sniff(['string', true, 123]));
			$this->assertTrue($this->sniffer->sniff([
				'key'	=>	'value',
				'flag'	=>	true,
				'count'	=>	42
			]));
		}

		public function testSniffStrict() {
			$this->assertTrue($this->sniffer->sniff(true, true));
			$this->assertTrue($this->sniffer->sniff(false, true));
			$this->assertTrue($this->sniffer->sniff('', true));
			$this->assertTrue($this->sniffer->sniff('string', true));
			$this->assertTrue($this->sniffer->sniff(null, true));
			$this->assertTrue($this->sniffer->sniff(123, true));
			$this->assertTrue($this->sniffer->sniff(123.456, true));
			$this->assertTrue($this->sniffer->sniff(INF, true));
			$this->assertTrue($this->sniffer->sniff(NAN, true));

			$this->assertFalse($this->sniffer->sniff([], true));
			$this->assertFalse($this->sniffer->sniff(['a', 'sequential', 'array'], true));
			$this->assertFalse($this->sniffer->sniff(['string', true, 123], true));
			$this->assertFalse($this->sniffer->sniff([
				'key'	=>	'value',
				'flag'	=>	true,
				'count'	=>	42
			], true));
		}
	}
