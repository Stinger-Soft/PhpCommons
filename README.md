# PhpCommons

Some common methods to ease the life of anonymous PHP developers

## String
* **startsWith** Tests if a string starts with the specified prefix.
* **endsWith** Tests if a string ends with the specified suffix
* **camelize** Uppercases the first character of each word in a string
* **excerpt** Creates an excerpt from a text based on a phrase
* **truncate** Truncates a given string after a given length (if necessary)
* **highlight** Highlights (i.e. adds html-tags arround the keyword) a given keyword in a string
* **hashCode** Get the integer based hash code of a given string

## Array
* **insertElement** Adds an element into a array on the given position without replacing the old entry
* **removeElementByValue** Removes the given element from the array
* **mergeArrayValues** Creates an array of arrays from the two given arrays.
* **getPrevKey** Returns the previous key from an array
* **getNextKey** Returns the next key from an array
* **applyCallbackByPath**  Applies a callback on a part of a multidimensional array defined by its path (i.e. keys)

## Integer
* **intcmp** Compares two integers (similarly to `strcmp`) and returns whether the first argument is smaller, less or equal to the second argument.
* **isInteger** Checks if a given value can be interpreted as a "real" integer without commas or exponent parts (i.e. `1e10` is *not* treated as an integer).

## Formatter

### Byte
* **prettyPrintSize** Pretty prints a given memory/file size (e.g. 1024 -> 1kB)

### Time
* **prettyPrintMicroTimeInterval** Pretty prints an interval specfied by two timestamps. 
* **getRelativeTimeDifference** Get the relative difference between a given start time and end time.


## HashCodeBuilder
Allows building a hash code by chaining different values defining an individual hash.
```php
$hashBuilder = new HashBuilder();
$hashCode = $hashBuilder
    ->append($item->getId())
    ->append($item->isEnabled())
    ->append($item->getName())
    ->toHashCode();
```

[![Build Status](https://travis-ci.org/Stinger-Soft/PhpCommons.svg?branch=develop)](https://travis-ci.org/Stinger-Soft/PhpCommons)
[![Coverage Status](https://coveralls.io/repos/github/Stinger-Soft/PhpCommons/badge.svg?branch=develop)](https://coveralls.io/github/Stinger-Soft/PhpCommons?branch=develop)
