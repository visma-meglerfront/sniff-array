<?php
	namespace Adepto\SniffArray\Sniff;

	class MixedArraySniffer extends SplSniffer {
		public static function isSequential(array $array): bool {
			return array_values($array) == $array;
		}

		public static function isAssociative(array $array): bool {
			return !count($array) || !self::isSequential($array);
		}

		protected function sniffVal($val, bool $isStrict = false): bool {
			return is_array($val) && (!$isStrict || count($val));
		}
	}