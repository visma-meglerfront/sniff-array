<?php
	namespace Adepto\SniffArray\Sniff;

	class MixedSniffer extends SplSniffer {
		public function sniff($val, bool $isStrict = false): bool {
			return !$isStrict || !is_array($val);
		}
	}