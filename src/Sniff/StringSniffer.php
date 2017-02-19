<?php
	namespace Adepto\SniffArray\Sniff;

	class StringSniffer extends SplSniffer {
		protected function sniffVal($val, bool $isStrict = false): bool {
			$match = is_string($val) && (!$isStrict || mb_strlen($val) > 0);

			if ($match && isset($this->specData[0])) {
				$pattern = self::wrapRegExp($this->specData[0]);
				$match = preg_match($pattern, $val) == 1;
			}

			return $match;
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

		public static function wrapRegExp(string $rawExpression): string {
			$wrappers = str_split('/#%?$');

			foreach ($wrappers as $wrapper) {
				if (mb_strpos($rawExpression, $wrapper) === false) {
					return $wrapper . $rawExpression . $wrapper;
				}
			}

			return $rawExpression;
		}
	}