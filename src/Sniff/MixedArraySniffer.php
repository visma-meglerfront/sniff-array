<?php
	namespace Adepto\SniffArray\Sniff;

	class MixedArraySniffer extends SplSniffer {
		const ARRAY_TYPE_REMAPPINGS = [
			'assoc'	=>	'associative',
			'seq'	=>	'sequential'
		];

		public static function isSequential(array $array): bool {
			return array_values($array) == $array;
		}

		public static function isAssociative(array $array): bool {
			return !count($array) || !self::isSequential($array);
		}

		protected function sniffVal($val, bool $isStrict = false): bool {
			$sniffMatch = is_array($val) && (!$isStrict || count($val));

			$specCount = count($this->specData);
			$typeMatch = !$specCount;

			if ($specCount > 0) {
				if ($specCount > 1) {
					if ($this->throw) {
						throw new \InvalidArgumentException('Conflicting colon specifiers for MixedArraySniffer set');
					}

					return false;
				}

				$desiredType = strtolower($this->specData[0]);
				$type = self::ARRAY_TYPE_REMAPPINGS[$desiredType] ?? $desiredType;

				if (!in_array($type, array_values(self::ARRAY_TYPE_REMAPPINGS))) {
					if ($this->throw) {
						throw new \InvalidArgumentException('Invalid array type for MixedArraySniffer: ' . $type);
					}

					return false;
				}

				$check = 'is' . ucfirst(strtolower($type));
				$typeMatch = is_array($val) && self::$check($val);
			}

			return $sniffMatch && $typeMatch;
		}
	}