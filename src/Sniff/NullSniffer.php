<?php
	namespace Adepto\SniffArray\Sniff;

	class NullSniffer extends SplSniffer {
		protected function sniffVal($val, bool $isStrict = false): bool {
			return is_null($val);
		}
	}