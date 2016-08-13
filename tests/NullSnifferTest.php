<?php
	use Adepto\SniffArray\Sniff\{
		NullSniffer, SplSniffer
	};

	class NullSnifferTest extends PHPUnit_Framework_TestCase {
		/** @var SplSniffer */
		protected $sniffer;

		protected function setUp() {
			$this->sniffer = new NullSniffer();
		}

		public function testStaticCreation() {
			$sniffer = SplSniffer::forType('null');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\NullSniffer', get_class($sniffer));

			$sniffer = SplSniffer::forType('empty');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\NullSniffer', get_class($sniffer));
		}

		public function testSniffPositive() {
			$this->assertTrue($this->sniffer->sniff(null));
		}

		public function testSniffNegative() {
			$this->assertFalse($this->sniffer->sniff(true));
			$this->assertFalse($this->sniffer->sniff(false));
			$this->assertFalse($this->sniffer->sniff(''));
			$this->assertFalse($this->sniffer->sniff('string'));
			$this->assertFalse($this->sniffer->sniff(123));
			$this->assertFalse($this->sniffer->sniff(0));
			$this->assertFalse($this->sniffer->sniff(-456));
			$this->assertFalse($this->sniffer->sniff(123456789));
			$this->assertFalse($this->sniffer->sniff(123.456));
			$this->assertFalse($this->sniffer->sniff(INF));
			$this->assertFalse($this->sniffer->sniff(NAN));
		}
	}