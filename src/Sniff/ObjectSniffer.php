<?php
	namespace Adepto\SniffArray\Sniff;

	class ObjectSniffer extends SplSniffer {
		protected function sniffVal($val, bool $isStrict = false): bool {
			try {
				$objClass = (new \ReflectionClass($val))->getShortName();
			} catch (\Exception $e) {
				$objClass = '';
			}

			foreach ($this->specData as $class) {
				if (($val instanceof $class) || $objClass == $class) {
					return true;
				}
			}

			return !count($this->specData) && is_object($val) && (!$isStrict || count((array) $val));
		}
	}