<?php
	namespace Adepto\SniffArray\Sniff;

	class IntSniffer extends SplSniffer {
		protected function sniffVal($val, bool $isStrict = false): bool {
			return is_integer($val) && (!$isStrict || !!$val);
		}
	}