<?php
	namespace Adepto\SniffArray\Sniff;

	use Adepto\SniffArray\Exception\InvalidArrayFormatException;

	/**
	 * Class ArraySniffer
	 * Can check an array for conformity to a certain specification
	 *
	 * @author  suushie_maniac
	 * @version 1.1
	 */
	class ArraySniffer {
		const ROOT_LEVEL = '__root';

		private $spec;
		private $throw;

		/**
		 * ArraySniffer constructor.
		 *
		 * @param array $spec The specification that this sniffer sniffs/checks for
		 * @param bool $throw If an exception should be raised on failure. A simple false is returned elsewise. Defaults to false
		 */
		public function __construct(array $spec, bool $throw = false) {
			$this->spec = $spec;
			$this->throw = $throw;
		}

		/**
		 * Set the exception throwing behaviour
		 *
		 * @param bool $throw Whether an exception should be raised on failure or not. Defaults to true
		 */
		public function setThrow(bool $throw = true) {
			$this->throw = $throw;
		}

		/**
		 * Sniff for the conformity of $array to this ArraySniffer instance's specification
		 *
		 * @param array $array The array to check conformity for
		 *
		 * @throws InvalidArrayFormatException If the $array doesn't match and $throw of this instance is true
		 *
		 * @return bool If the $array matches or not
		 */
		public function sniff(array $array): bool {
			if (MixedArraySniffer::isSequential($array) && !array_intersect(array_keys($array), array_keys($this->spec))) {
				$array = [static::ROOT_LEVEL	=>	array_values($array)];
			}

			foreach ($this->spec as $key => $type) {
				$key = preg_replace('/(.*)\*$/', '$1{,}', $key);
				$key = preg_replace('/(.*)\+$/', '$1{1,}', $key);
				$key = preg_replace('/(.*)\?$/', '$1{,1}', $key);

				$key = preg_replace('/(.*){(\d+)}$/', '$1{$2,$2}', $key);

				$baseKey = preg_replace('/(.*){\d*,\d*}$/', '$1', $key);
				$element = $array[$baseKey] ?? null;

				if ($baseKey != $key) { //RegExp key used
					$min = (int) preg_replace('/.*{(\d*),\d*}$/', '$1', $key) ?: 0;
					$max = (int) preg_replace('/.*{\d*,(\d*)}$/', '$1', $key) ?: INF;

					$subSpec = [$baseKey => $type];
					$subSniffer = $this->subSniffer($subSpec);

					$mayDrop = $min == 0;

					if (!is_array($element)
						|| (is_array($type) && array_keys($type) == array_keys($element))
						|| (is_string($type) && SplSniffer::forType($type) instanceof MixedArraySniffer)) {
						$element = $mayDrop && is_null($element) ? [] : [$element];
					}

					$elemCount = count($element);
					$conforms = $min <= $elemCount && $elemCount <= $max;

					foreach ($element as $subElement) {
						$subElement = [$baseKey => $subElement];
						$conforms &= $subSniffer->sniff($subElement);
					}

					if (!$this->handle(!$conforms, 'Key ' . $key . ' of type ' . json_encode($type) . ' does not conform!')) {
						return false;
					}
				} else if (is_array($type)) {
					if (!$this->handle(!array_key_exists($key, $array), 'Missing key: ' . $key . ' of type ' . json_encode($type))) {
						return false;
					}

					if (!$this->handle(!is_array($element), $key . ' must be a complex array')) {
						return false;
					}

					$conforms = $this->subSniffer($type)->sniff($element);

					if (!$this->handle(!$conforms, 'Complex array ' . $key . ' does not conform!')) {
						return false;
					}
				} else {
					$expectedTypes = preg_split('/\|(?=' . implode('|', SplSniffer::getSupportedTypes()) . ')/', $type);

					$conforms = false;

					if (!$this->handle(!array_key_exists($key, $array) && !in_array('null', $expectedTypes), 'Missing key: ' . $key . ' of type ' . $type)) {
						return false;
					}

					foreach ($expectedTypes as $t) {
						$baseType = preg_replace('/(.*)\!$/', '$1', $t);
						$isStrict = $t != $baseType;

						if (!$this->handle(!SplSniffer::isValidType($baseType), 'Type ' . $baseType . ' not valid')) {
							return false;
						}

						$conforms |= SplSniffer::forType($baseType)->sniff($element, $isStrict);
					}

					if (!$this->handle(!$conforms, $key . ' with value ' . var_export($element, true) . ' does not match type definition ' . $type)) {
						return false;
					}
				}
			}

			return true;
		}

		private function subSniffer(array $spec): ArraySniffer {
			return new self($spec, $this->throw);
		}

		private function handle(bool $condition, string $errMessage = ''): bool {
			if ($condition) {
				if ($this->throw) {
					throw new InvalidArrayFormatException($errMessage);
				}

				return false;
			} else {
				return true;
			}
		}

		/**
		 * Check if an array conforms to a specification
		 *
		 * @param array $spec The specification to check for
		 * @param array $array The array to check
		 * @param bool $throw Whether an exception should be raised on failure
		 *
		 * @throws InvalidArrayFormatException If the $array doesn't match and $throw is set to true
		 *
		 * @return bool If the $array matches or not
		 */
		public static function arrayConformsTo(array $spec, array $array, bool $throw = false): bool {
			return (new ArraySniffer($spec, $throw))->sniff($array);
		}
	}