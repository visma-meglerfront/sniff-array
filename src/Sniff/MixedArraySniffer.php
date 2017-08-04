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
			return is_array($val) && (!$isStrict || count($val));
		}

		protected function sniffColonVal($val, string $colonData): bool {
			$desiredType = strtolower($this->specData[0]);
			$type = self::ARRAY_TYPE_REMAPPINGS[$desiredType] ?? $desiredType;

			if (!in_array($type, array_values(self::ARRAY_TYPE_REMAPPINGS))) {
				if ($this->throw) {
					throw new \InvalidArgumentException('Invalid array type for MixedArraySniffer: ' . $type);
				}

				return false;
			}

			$check = 'is' . ucfirst(strtolower($type));
			return self::$check($val);
		}
	}