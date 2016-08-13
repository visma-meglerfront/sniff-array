<?php
	namespace Adepto\SniffArray\Sniff;

	class NumberSniffer extends SplSniffer {
		protected function sniffVal($val, bool $isStrict = false): bool {
			return is_numeric($val) && (!$isStrict || (!!$val && !is_nan($val)));
		}
	}