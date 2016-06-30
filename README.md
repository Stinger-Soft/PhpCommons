# PhpCommons

Some common methods to ease the life of anonymous PHP developer

## String
* **startsWith** Tests if a string starts with the specified prefix.
* **endsWith** Tests if a string ends with the specified suffix
* **camelize** Uppercases the first character of each word in a string
* **excerpt** Creates an excerpt from a text based on a phrase
* **highlight** Highlights (i.e. adds html-tags arround the keyword) a given keyword in a string

## Array
* **insertElement** Adds an element into a array on the given position without replacing the old entry
* **removeElementByValue** Removes the given element from the array

## Formatter

### Byte
* **prettyPrintSize** Pretty prints a given memory/file size (e.g. 1024 -> 1kB)

### Time
* **prettyPrintMicroTimeInterval** Pretty prints an interval specfied by two timestamps. 
* **getRelativeTimeDifference** Get the relative difference between a given start time and end time.


[![Build Status](https://travis-ci.org/Stinger-Soft/PhpCommons.svg?branch=master)](https://travis-ci.org/Stinger-Soft/PhpCommons)
[![Coverage Status](https://coveralls.io/repos/github/Stinger-Soft/PhpCommons/badge.svg?branch=master)](https://coveralls.io/github/Stinger-Soft/PhpCommons?branch=master)
