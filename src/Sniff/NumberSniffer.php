<?php
	namespace Adepto\SniffArray\Sniff;

	class NumberSniffer extends SplSniffer {
		public function sniff($val, bool $isStrict = false): bool {
			return is_numeric($val) && (!$isStrict || (!!$val && !is_nan($val)));
		}
	}