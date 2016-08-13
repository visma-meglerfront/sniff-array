<?php
	namespace Adepto\SniffArray\Sniff;

	class ArrayFlatSniffer extends SplSniffer {
		public function sniffVal($val, bool $isStrict = false): bool {
			return is_array($val) && (!$isStrict || count($val));
		}
	}