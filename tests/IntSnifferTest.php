<?php
	use Adepto\SniffArray\Sniff\SplSniffer;

	class IntSnifferTest extends PHPUnit_Framework_TestCase {
		/** @var SplSniffer */
		private $sniffer;

		protected function setUp() {
			$this->sniffer = SplSniffer::forType('int');
		}

		public function testStaticCreation() {
			$sniffer = SplSniffer::forType('int');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\IntSniffer', get_class($sniffer));

			$sniffer = SplSniffer::forType('integer');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\IntSniffer', get_class($sniffer));
		}

		public function testSniffPositive() {
			$this->assertTrue($this->sniffer->sniff(123));
			$this->assertTrue($this->sniffer->sniff(0));
			$this->assertTrue($this->sniffer->sniff(-456));
			$this->assertTrue($this->sniffer->sniff(123456789));
		}

		public function testSniffNegative() {
			$this->assertFalse($this->sniffer->sniff(true));
			$this->assertFalse($this->sniffer->sniff(false));
			$this->assertFalse($this->sniffer->sniff(''));
			$this->assertFalse($this->sniffer->sniff('string'));
			$this->assertFalse($this->sniffer->sniff(123.456));
			$this->assertFalse($this->sniffer->sniff(INF));
			$this->assertFalse($this->sniffer->sniff(NAN));
			$this->assertFalse($this->sniffer->sniff(null));
		}

		public function testSniffStrict() {
			$this->assertTrue($this->sniffer->sniff(123, true));
			$this->assertTrue($this->sniffer->sniff(-456, true));
			$this->assertTrue($this->sniffer->sniff(123456789, true));

			$this->assertFalse($this->sniffer->sniff(0, true));
		}
	}
