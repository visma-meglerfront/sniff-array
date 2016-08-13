<?php
	namespace Adepto\SniffArray\Sniff;

	class MixedSniffer extends SplSniffer {
		public function sniffVal($val, bool $isStrict = false): bool {
			return !$isStrict || !is_array($val);
		}
	}