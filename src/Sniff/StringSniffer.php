<?php
	namespace Adepto\SniffArray\Sniff;

	class StringSniffer extends SplSniffer {
		public function sniffVal($val, bool $isStrict = false): bool {
			return is_string($val) && (!$isStrict || mb_strlen($val) > 0);
		}

		public static function strEndsWith(string $haystack, string $needle): bool {
			return mb_strpos(strrev($haystack), strrev($needle)) === 0;
		}

		public static function capitalize(string $string, bool $strict = false): string {
			$string = trim($string);

			$first = mb_substr($string, 0, 1);
			$rest = mb_substr($string, 1);

			return mb_strtoupper($first) . ($strict ? mb_strtolower($rest) : $rest);
		}
	}