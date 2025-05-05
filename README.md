# Pathlib

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

`Crell/Path` is a simple library that provides typed value objects for handling paths.  Paths may be either a `PathFragment` (no leading `/`) or an `AbsolutePath`.  An `AbsolutePath` always begins either with a `/` or a stream identifier.

At this time, paths are treated as abstract values and not coupled to a file system.  That functionality may be included at some point in the future.  Other feature requests (preferably with accompanying pull requests) are welcome.

A Path object's primary purpose is to centralize and abstract away the various and sundry complexities and edge cases of manipulating paths: Getting a parent path (have to handle the case where there isn't one, with or without a stream), Concatenating two paths (have to handle the leading/trailing slashes, accounting for when one path or the other is the root path), and so on.

```php
use Crell\Path\Path;

// Creates a Path Fragment
$frag = Path::create('foo/bar');

// Creates an Absolute Path
$abs = Path::create('/baz/beep');

$new = $abs->concat($frag);

// Prints /baz/beep/foo/bar
print $new;

// Prints /baz/beep/foo
print $new->parent();

$file = '/narf.jpg';
$fileAbs = $abs->concat($file);

// Prints /baz/beep/narf.jpg
print $fileAbs;
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please use the [GitHub security reporting form](https://github.com/Crell/Path/security) rather than the issue queue.

## Credits

- [Larry Garfield][link-author]
- [All Contributors][link-contributors]

## License

The Lesser GPL version 3 or later. Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/Crell/Path.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/License-LGPLv3-green.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/Crell/Path.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/Crell/Path
[link-scrutinizer]: https://scrutinizer-ci.com/g/Crell/Path/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Crell/Path
[link-downloads]: https://packagist.org/packages/Crell/Path
[link-author]: https://github.com/Crell
[link-contributors]: ../../contributors
