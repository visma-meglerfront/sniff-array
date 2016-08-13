<?php
	namespace Adepto\SniffArray\Sniff;

	class BoolSniffer extends SplSniffer {
		public function sniff($val, bool $isStrict = false): bool {
			return is_bool($val);
		}
	}