<?php
	namespace Adepto\SniffArray\Sniff;

	class MixedArraySniffer extends SplSniffer {
		protected function sniffVal($val, bool $isStrict = false): bool {
			return is_array($val) && (!$isStrict || count($val));
		}
	}