<?php
	namespace Adepto\SniffArray\Sniff;

	class NullSniffer extends SplSniffer {
		public function sniff($val, bool $isStrict = false): bool {
			return is_null($val);
		}
	}