# SniffArray

This is a lightweight library to check if a given array conforms to a certain specification

## Installation

Add this to `composer.json`:

```json
"repositories": [
	{
		"type": "vcs",
		"url": "https://github.com/adeptoas/sniffarray"
	}
],

"require": {
	"adeptoas/sniffArray": "^1.0.0"
}
```

Make sure to merge your `require`-blocks!

## Usage

### ArraySniffer

```php
__construct(array $spec, bool $throw = false)
```

Initialize an ArraySniffer with the given `spec`. Error handling can be specified via `throw`, else every sniff will simply return `false`.

```php
sniff(array $array): bool
```

Start checking `array` for conformity in respect to the current `ArraySniffer` instance's specification.

```php
static arrayConformsTo(array $spec, array $data, bool $throw = false): bool
```

Check if `array` conforms to `spec`. This does the same as calling `sniff` on an `ArraySniffer` instance, but is mainly for one-time-only checks

### SplSniffer

This library provides several sniffers for primitive / SPL types in PHP. They are mainly needed for internal verification purposes, but you can use them as stand-alone verification sniffers if you want to.

```php
static forType(string $type): SplSniffer
```

Return a subclass of `SplSniffer` suitable for checking conformity to `type`.

```php
sniff(mixed $val, bool $isStrict = false): bool
```

Check if `val` conforms to the specification implied by the current `SplSniffer` instance's type. `isStrict` specifies if certain edge cases should be deemed valid or not (see below for further explanation)

## Specification

### Base types

The basic type identifications delivered with this library are
* string
* int
* number
* bool
* mixed
* array

Some common aliases are also implemented:
* boolean => bool
* integer => int
* numeric => number
* any => mixed
* empty => null

Strict specifications are denoted by the type followed by an exclamation sign (!) and narrow down the possible accepted values. In particular:
* string! doesn't accept the empty string `''`
* int! doesn't accept `0`
* number! doesn't accept `0` or `NAN`
* array! doesn't accept empty arrays `[]`
* mixed! doesn't accept arbitrary arrays, only pure primitives

`array` as primitive specification simply denotes any arbitrary array of whatever form, while an explicitly specified nested array specification demands exact matches

### Array specifications

Any array specification is itself arranged as an associative array.

```php
[
    'key'       =>  'string',
    'otherKey'  =>  'int'
]
```

for example is a valid specification for

```php
[
    'key'       =>  'value',
    'otherKey'  =>  42
]
```

Specifications can of course be nested.

```php
[
    'key'   =>  [
        'first'     =>  'number',
        'second'    =>  'bool'
    ]
]
```

matches for example

```php
[
    'key'   =>  [
        'first'     =>  INF,
        'second'    =>  false
    ]
]
```

but (obviously) not

```php
[
    'key'   =>  'someString'
]
```

Specification keys can be appended by some RegExp-like features to expand matching functionality, namely:
* `+` for one or more matches
* `*` for zero or more matches
* `?` for an optional match
* `{a,b}` for minimum a and maximum b matches
  * `{,b}` for zero to b matches
  * `{a,}` for a to INF matches
  
Keys that allow for 0 matches by use of this RegExp typeset can either be explicitly set to `null` or implicitly dropped.

These RegExp rules can of course also be applied to nested array specification structures.
```php
[
    'key'       =>  'string',
    'optional?' =>  'int',
    'any*'      =>  [
        'foo'   =>  'bool',
        'bar'   =>  [
            'one+'      =>  'number!',
            'two{3,5}'  =>  'number!'
        ]
    ]
]
```

matches

```php
[
    'key'       =>  'value',
    'optional'  =>  0,
    'any'       =>  [
        [
            'foo'   =>  true,
            'bar'   =>  [
               'one'   =>  123,
               'two'   =>  [456, 789, 321, 654]
            ]
        ], [
            'foo'   =>  false,
            'bar'   =>  [
               'one'   =>  [3.141592, 6.283185],
               'two'   =>  [1.414213, 2.718281, 1.618033]
            ]
        ]
    ]
]
```

as well as

```php
[
    'key'       =>  'value',
    'any'      =>  [
        'foo'   =>  true,
        'bar'   =>  [
            'one'   =>  123,
            'two'   =>  [1.1, 2, 3.3, 4, 5.5]
        ]
    ]
]
```

## Examples

Examples will be added to the `examples/` directory once I come up with meaningful examples that don't involve confidential data from my dayjob.