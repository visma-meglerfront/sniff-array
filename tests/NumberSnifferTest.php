<?php
	use Adepto\SniffArray\Sniff\SplSniffer;

	class NumberSnifferTest extends PHPUnit_Framework_TestCase {
		/** @var SplSniffer */
		private $sniffer;

		protected function setUp() {
			$this->sniffer = SplSniffer::forType('number');
		}

		public function testStaticCreation() {
			$sniffer = SplSniffer::forType('number');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\NumberSniffer', get_class($sniffer));

			$sniffer = SplSniffer::forType('numeric');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\NumberSniffer', get_class($sniffer));
		}

		public function testSniffPositive() {
			$this->assertTrue($this->sniffer->sniff(123));
			$this->assertTrue($this->sniffer->sniff(0));
			$this->assertTrue($this->sniffer->sniff(-456));
			$this->assertTrue($this->sniffer->sniff(123456789));
			$this->assertTrue($this->sniffer->sniff(123.456));
			$this->assertTrue($this->sniffer->sniff(INF));
			$this->assertTrue($this->sniffer->sniff(NAN));
		}

		public function testSniffNegative() {
			$this->assertFalse($this->sniffer->sniff(true));
			$this->assertFalse($this->sniffer->sniff(false));
			$this->assertFalse($this->sniffer->sniff(''));
			$this->assertFalse($this->sniffer->sniff('string'));
			$this->assertFalse($this->sniffer->sniff(null));
		}

		public function testSniffStrict() {
			$this->assertTrue($this->sniffer->sniff(123, true));
			$this->assertTrue($this->sniffer->sniff(-456, true));
			$this->assertTrue($this->sniffer->sniff(123456789, true));
			$this->assertTrue($this->sniffer->sniff(123.456, true));
			$this->assertTrue($this->sniffer->sniff(INF, true));

			$this->assertFalse($this->sniffer->sniff(0, true));
			$this->assertFalse($this->sniffer->sniff(NAN, true));
		}
	}
