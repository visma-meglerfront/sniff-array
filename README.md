# SniffArray

This is a lightweight library to check if a given array conforms to a certain specification

## Installation

Add this to `composer.json`:

```json
"require": {
	"adeptoas/sniff-array": "^1.0.0"
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
* object

Some common aliases are also implemented:
* boolean => bool
* integer => int
* numeric => number
* any => mixed
* empty => null
* class => object

Strict specifications are denoted by the type followed by an exclamation sign `!` and narrow down the possible accepted values. In particular:
* string! doesn't accept the empty string `''`
* int! doesn't accept `0`
* number! doesn't accept `0` or `NAN`
* array! doesn't accept empty arrays `[]`
* object! doesn't accept stdClass objects with 0 fields `new stdClass()`
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
* `{a,b}` for minimum a and maximum b matches
  * `{,b}` for zero to b matches
  * `{a,}` for a to INF matches
* `+` for one or more matches (equal to `{1,}`)
* `*` for zero or more matches (equal to `{0,}`)
* `?` for an optional match (equal to `{0,1}`)
  
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
    'any'       =>  [
        'foo'   =>  true,
        'bar'   =>  [
            'one'   =>  123,
            'two'   =>  [1.1, 2, 3.3, 4, 5.5]
        ]
    ]
]
```

Multiple types can be joined by using the bar `|` sign. This does not infer with any of the other type rules.

```php
[
    'key'       =>  'string!|bool',
    'otherKey'  =>  'int|array'
]
```

is a valid specification for

```php
[
    'key'       =>  'element',
    'otherKey'  =>  123
]
```

as well as

```php
[
    'key'       =>  true,
    'otherKey'  =>  123
]
```

and

```php
[
    'key'       =>  false,
    'otherKey'  =>  ['foo', 'bar']
]
```

Arrays that are sequential at root level can be checked for specification match by using the key `__root`. This can be especially useful for bulk mode handling a given specification

```php
[
    '__root+'   =>  [
        'foo'       =>  'bool',
        'bar?'      =>  'int'
    ]
]
```

matches

```php
[
    [
        'foo'   =>  'true',
        'bar'   =>  0
    ], [
        'foo'   =>  'true'
    ], [
        'foo'   =>  'false',
        'bar'   =>  42
    ]
]
```

## Colon notation

Some Sniffers, particularly `StringSniffer`, `ObjectSniffer` and `MixedArraySniffer`, support specifying additional sniff data
via the usage of `::`

This colon "operator" supports multiple repeated arguments. The effect of an argument is specified by the respective Sniffer class.

For `StringSniffer`, the colon data can be used to specify a matching regular expression (RegExp).
The conformity to this RegExp will be checked in addition to the usual check by using `preg_match` as specified in the PHP standard library

```php
[
    'foo*'   =>  'string::^[A-Z][a-z]*$'
]
```

matches

```php
[
    'foo'   =>  ['Hello', 'World']
]
```

but not

```php
[
    'foo'   =>  ['eHlo', 'World', '!']
]
```

Please note that the RegExp will automatically be wrapped in convenient PCRE bounds, (i.e. //) if not wrapped already

For `ObjectSniffer`, the colon data can be used to specify instance class names. They can be fully namespaced,
but standard "short name" checks will also be performed.

```php
[
    'object'   =>  'class::MyClass'
]
```

matches

```php
[
    'object'   =>  new MyClass()
]
```

but neither

```php
[
    'object'   =>  new MyOtherClass()
]
```

nor

```php
[
    'object'   =>  new stdClass()
]
```

Please also note that multiple classes can be specified using the same colon `::` operator in a repeated fashion

```php
[
    'object'   =>  'class::MyClass::MyOtherClass'
]
```

accounts for both

```php
[
    'object'   =>  new MyClass()
]
```

and

```php
[
    'object'   =>  new MyOtherClass()
]
```

For `MixedArraySniffer`, the colon data can be used to specify whether an associative or a sequential array is desired.

```php
[
    'values'   =>  'array::sequential'
]
```

matches

```php
[
    'values'    =>  [1, 2, 'one', 'two', true, false]
]
```

but not

```php
[
    'values'    =>  [
        'key'   =>  'value'
    ]
]
```

whereas the specification

```php
[
    'dict'   =>  'array::associative'
]
```

matches

```php
[
    'dict'  =>  [
        'one'   =>  1,
        'two'   =>  2
    ]
]
```

but not the `values` example from above. Common aliases `seq` for `sequential` and `assoc` for `associative` are installed for convenience,
so that

```php
[
    'members'   =>  'array::seq',
    'relations' =>  'array::assoc'
]
```

is a valid specification. More than one specified array type will be discarded and sniffed as `false` or throw an exception, respectively.

## Examples

Examples will be added to the `Examples/` directory once I come up with meaningful examples that don't involve confidential data from my dayjob.