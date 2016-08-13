<?php
	namespace Adepto\SniffArray\Sniff;

	class ArrayFlatSniffer extends SplSniffer {
		public function sniff($val, bool $isStrict = false): bool {
			return is_array($val) && (!$isStrict || count($val));
		}
	}