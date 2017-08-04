<?php
	namespace Adepto\SniffArray\Sniff;

	class ObjectSniffer extends SplSniffer {
		protected function sniffVal($val, bool $isStrict = false): bool {
			return is_object($val) && (!$isStrict || count((array) $val));
		}

		protected function sniffColonVal($val, string $colonData): bool {
			try {
				$objClass = (new \ReflectionClass($val))->getShortName();
			} catch (\Exception $e) {
				$objClass = '';
			}

			return $val instanceof $colonData || $objClass == $colonData;
		}
	}