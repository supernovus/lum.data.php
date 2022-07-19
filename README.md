# lum.data.php

## Summary

Implements a bunch of magic *data objects* that I've been using for a long
time as they were designed to convert between various serialization formats
mostly transparently.

## Namespace

`Lum\Data`

### Classes

| Name                    | Description                                       |
| ----------------------- | ------------------------------------------------- |
| Lum\Data\O              | A *very minimal* base class for *data objects*.   |
| Lum\Data\Obj            | Default base class with *most* traits added.      |
| Lum\Data\Arrayish       | An extension of `Obj` using `Arraylike` trait.    |
| Lum\Data\Container      | An extension of `Arrayish` with indexed children. |


### Traits

| Name                    | Description                                       |
| ----------------------- | ------------------------------------------------- |
| Lum\Data\Arraylike      | A trait for array-like data objects.              |
| Lum\Data\DetectType     | A trait with helpers for detecting input type.    |
| Lum\Data\JSON           | A trait for objects with JSON representations.    |
| Lum\Data\BuildXML       | A trait with helpers for building XML data.       |
| Lum\Data\InputXML       | A trait for data objects that can import XML.     |
| Lum\Data\OutputXML      | A trait for data objects that can output XML.     |

## Official URLs

This library can be found in two places:

 * [Github](https://github.com/supernovus/lum.data.php)
 * [Packageist](https://packagist.org/packages/lum/lum-data)

## Authors

- Timothy Totten

## License

[MIT](https://spdx.org/licenses/MIT.html)
