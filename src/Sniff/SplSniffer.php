<?php
	namespace Adepto\SniffArray\Sniff;

	use Adepto\SniffArray\Exception\ClassNotFoundException;

	abstract class SplSniffer {
		const BASE_NAMESPACE = 'Adepto\\SniffArray\\Sniff';

		const TYPE_BOOL = 'bool';
		const TYPE_STRING = 'string';
		const TYPE_INT = 'int';
		const TYPE_NUMBER = 'number';
		const TYPE_NULL = 'null';

		const SUPPORTED_TYPES = [
			self::TYPE_BOOL,
			self::TYPE_STRING,
			self::TYPE_INT,
			self::TYPE_NUMBER,
			self::TYPE_NULL
		];

		const TYPE_REMAPPINGS = [
			'boolean'	=>	self::TYPE_BOOL,
			'empty'		=>	self::TYPE_NULL,
			'numeric'	=>	self::TYPE_NUMBER,
			'integer'	=>	self::TYPE_INT
		];

		public static function forType(string $type, bool $throw = false): SplSniffer {
			$type = static::TYPE_REMAPPINGS[$type] ?? $type;

			$classType = StringSniffer::humanize($type);
			$snifferClass = self::BASE_NAMESPACE . '\\' . $classType . 'Sniffer';

			if (class_exists($snifferClass)) {
				return new $snifferClass($throw);
			} else {
				throw new ClassNotFoundException($snifferClass . ' for type ' . $type . ' not found');
			}
		}

		protected $throw;

		public function __construct(bool $throw = false) {
			$this->throw = $throw;
		}

		public abstract function sniff($val, bool $isStrict = false): bool;

		public static function validateTypes(array $types): bool {
			$valid = true;

			foreach ($types as $type) {
				if (!is_string($type))
					return false;

				$type = static::TYPE_REMAPPINGS[$type] ?? $type;
				$valid &= in_array($type, static::SUPPORTED_TYPES);
			}

			return $valid;
		}

		public static function isValidType(string $type): bool {
			$type = static::TYPE_REMAPPINGS[$type] ?? $type;
			return in_array($type, static::SUPPORTED_TYPES);
		}
	}