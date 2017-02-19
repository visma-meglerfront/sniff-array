<?php
	namespace Adepto\SniffArray\Sniff;

	class ObjectSniffer extends SplSniffer {
		const CLASS_DELIMITER = '|';

		protected function sniffVal($val, bool $isStrict = false): bool {
			if (isset($this->specData[0])) {
				$classes = explode(self::CLASS_DELIMITER, $this->specData[0]);

				try {
					$objClass = (new \ReflectionClass($val))->getShortName();
				} catch (\Exception $e) {
					$objClass = '';
				}

				foreach ($classes as $class) {
					if (($val instanceof $class) || $objClass == $class) {
						return true;
					}
				}

				return false;
			} else {
				return is_object($val) && (!$isStrict || count((array) $val));
			}
		}
	}