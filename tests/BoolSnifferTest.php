<?php
	use Adepto\SniffArray\Sniff\{
		BoolSniffer, SplSniffer
	};

	class BoolSnifferTest extends PHPUnit_Framework_TestCase {
		/** @var SplSniffer */
		protected $sniffer;

		protected function setUp() {
			$this->sniffer = SplSniffer::forType('bool');
		}

		public function testStaticCreation() {
			$sniffer = SplSniffer::forType('bool');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\BoolSniffer', get_class($sniffer));

			$sniffer = SplSniffer::forType('boolean');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\BoolSniffer', get_class($sniffer));
		}

		public function testSniffPositive() {
			$this->assertTrue($this->sniffer->sniff(true));
			$this->assertTrue($this->sniffer->sniff(false));
		}

		public function testSniffNegative() {
			$this->assertFalse($this->sniffer->sniff(''));
			$this->assertFalse($this->sniffer->sniff('string'));
			$this->assertFalse($this->sniffer->sniff(null));
			$this->assertFalse($this->sniffer->sniff(123));
			$this->assertFalse($this->sniffer->sniff(123.456));
			$this->assertFalse($this->sniffer->sniff(INF));
			$this->assertFalse($this->sniffer->sniff(NAN));
		}
	}
