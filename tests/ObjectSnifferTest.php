<?php
	use Adepto\SniffArray\Sniff\SplSniffer;

	class ObjectSnifferTest extends PHPUnit_Framework_TestCase {
		/** @var SplSniffer */
		protected $sniffer;
		/** @var SplSniffer */
		protected $instanceSniffer;

		protected function setUp() {
			$this->sniffer = SplSniffer::forType('object');
			$this->instanceSniffer = SplSniffer::forType('class::IntSniffer::MixedSniffer');
		}

		public function testStaticCreation() {
			$sniffer = SplSniffer::forType('object');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\ObjectSniffer', get_class($sniffer));

			$sniffer = SplSniffer::forType('class');
			$this->assertEquals('Adepto\\SniffArray\\Sniff\\ObjectSniffer', get_class($sniffer));
		}

		public function testSniffPositive() {
			$this->assertTrue($this->sniffer->sniff(new stdClass()));
			$this->assertTrue($this->sniffer->sniff((object) ['a', 'sequential', 'array']));
			$this->assertTrue($this->sniffer->sniff((object) ['string', true, 123]));
			$this->assertTrue($this->sniffer->sniff((object) [
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
			$this->assertFalse($this->sniffer->sniff([]));
			$this->assertFalse($this->sniffer->sniff(['a', 'sequential', 'array']));
			$this->assertFalse($this->sniffer->sniff(['string', true, 123]));
			$this->assertFalse($this->sniffer->sniff([
				'key'	=>	'value',
				'flag'	=>	true,
				'count'	=>	42
			]));
		}

		public function testSniffStrict() {
			$this->assertTrue($this->sniffer->sniff((object) ['a', 'sequential', 'array'], true));
			$this->assertTrue($this->sniffer->sniff((object) ['string', true, 123], true));
			$this->assertTrue($this->sniffer->sniff((object) [
				'key'	=>	'value',
				'flag'	=>	true,
				'count'	=>	42
			], true));

			$this->assertFalse($this->sniffer->sniff(new stdClass(), true));
		}

		public function testSniffColon() {
			$this->assertTrue($this->instanceSniffer->sniff(SplSniffer::forType('int')));
			$this->assertTrue($this->instanceSniffer->sniff(SplSniffer::forType('any')));

			$this->assertFalse($this->instanceSniffer->sniff(SplSniffer::forType('array')));
			$this->assertFalse($this->instanceSniffer->sniff('random'));
			$this->assertFalse($this->instanceSniffer->sniff(null));
		}
	}