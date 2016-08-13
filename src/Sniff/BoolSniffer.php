<?php
	namespace Adepto\SniffArray\Sniff;

	class BoolSniffer extends SplSniffer {
		public function sniffVal($val, bool $isStrict = false): bool {
			return is_bool($val);
		}
	}