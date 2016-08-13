<?php
	namespace Adepto\SniffArray\Sniff;

	class MixedSniffer extends SplSniffer {
		protected function sniffVal($val, bool $isStrict = false): bool {
			return !$isStrict || !is_array($val);
		}
	}