<?php
	use Adepto\SniffArray\Sniff\{
		SplSniffer, StringSniffer
	};

	class StringSnifferTest extends PHPUnit_Framework_TestCase {
		/** @var SplSniffer */
		private $sniffer;

		protected function setUp() {
			$this->sniffer = SplSniffer::forType('string');
		}

		public function testStaticCreation() {
			$sniffer = SplSniffer::forType('string');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\StringSniffer', get_class($sniffer));
		}

		public function testStaticStrEndsWith() {
			$this->assertTrue(StringSniffer::strEndsWith('endsWith', 'With'));
			$this->assertTrue(StringSniffer::strEndsWith('string*', '*'));

			$this->assertFalse(StringSniffer::strEndsWith('endsWith', 'nope'));
			$this->assertFalse(StringSniffer::strEndsWith('endsWith', 'Without'));
			$this->assertFalse(StringSniffer::strEndsWith('endsWith', 'with'));
			$this->assertFalse(StringSniffer::strEndsWith('string+', '*'));
		}

		public function testStaticCapitalize() {
			$this->assertEquals('Capitalize', StringSniffer::capitalize('capitalize'));
			$this->assertEquals('Capitalize', StringSniffer::capitalize('Capitalize'));
			$this->assertEquals('CApitalize', StringSniffer::capitalize('CApitalize'));
			$this->assertEquals('CApItALizE', StringSniffer::capitalize('cApItALizE'));
			$this->assertEquals('CAPITALIZE', StringSniffer::capitalize('CAPITALIZE'));
			$this->assertEquals('CAPITALIZE', StringSniffer::capitalize('cAPITALIZE'));

			$this->assertEquals('Capitalize', StringSniffer::capitalize('capitalize', true));
			$this->assertEquals('Capitalize', StringSniffer::capitalize('Capitalize', true));
			$this->assertEquals('Capitalize', StringSniffer::capitalize('CApitalize', true));
			$this->assertEquals('Capitalize', StringSniffer::capitalize('cApItALizE', true));
			$this->assertEquals('Capitalize', StringSniffer::capitalize('CAPITALIZE', true));
			$this->assertEquals('Capitalize', StringSniffer::capitalize('cAPITALIZE', true));

			$this->assertNotEquals('capitalize', StringSniffer::capitalize('capitalize'));
			$this->assertNotEquals('CAPITALIZE', StringSniffer::capitalize('capitalize'));
			$this->assertNotEquals('Capitalize', StringSniffer::capitalize('nope'));

			$this->assertNotEquals('capitalize', StringSniffer::capitalize('capitalize', true));
			$this->assertNotEquals('CAPITALIZE', StringSniffer::capitalize('capitalize', true));
			$this->assertNotEquals('Capitalize', StringSniffer::capitalize('nope', true));
		}

		public function testSniffPositive() {
			$this->assertTrue($this->sniffer->sniff('string'));
			$this->assertTrue($this->sniffer->sniff(''));
		}

		public function testSniffNegative() {
			$this->assertFalse($this->sniffer->sniff(true));
			$this->assertFalse($this->sniffer->sniff(false));
			$this->assertFalse($this->sniffer->sniff(null));
			$this->assertFalse($this->sniffer->sniff(123));
			$this->assertFalse($this->sniffer->sniff(0));
			$this->assertFalse($this->sniffer->sniff(-456));
			$this->assertFalse($this->sniffer->sniff(123456789));
			$this->assertFalse($this->sniffer->sniff(123.456));
			$this->assertFalse($this->sniffer->sniff(INF));
			$this->assertFalse($this->sniffer->sniff(NAN));
		}

		public function testSniffStrict() {
			$this->assertTrue($this->sniffer->sniff('string', true));

			$this->assertFalse($this->sniffer->sniff('', true));
		}
	}
