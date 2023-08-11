# Changelog
Fixed issue where `variantUrlsForTarget()` returns invalid urls

## 2.*

### [2.1.4] - 2023-08-11

Fixed an issue that broke error handling due to a mistyped exception method.

### [2.1.3] - 2022-08-07

Fixed + vs %20 for spaces in encoded filenames.

### [2.1.2] - 2022-06-15

Fixed an issue concerning invalid characters in filename-generated URLs (by TSVitor).

### [2.1.1] - 2022-03-27

Added support for dependencies of Laravel 9. This fixes the support for Paperclip.

### [2.0.3] - 2020-10-20

Fixed an issue where `<width>x` (without a height value) would cause resizing to be interpreted as portrait rather than landscape erroneously.

### [2.0.1] - 2020-05-13

All code updated to make use of PHP 7.1+ typehinting and return types.
This is quite the breaking change as all the interfaces changed.
Now requires PHP 7.1+.

Added the `openStream()` and `closeStream()` methods to the StorableFileInterface.
For (some) storable file instances, this may help to set up more memory-friendly content handling.
For locally stored files, you can get a `resource` object this way, just as you would if you `fopen()`'d a file directly.


## 1.*

### [1.3.1] - 2020-03-19

Added support for Symfony 5 (and so Laravel 7).
Added support for newer illuminate contracts (for testing).

### [1.2.1] - 2019-11-12

Fixed deprecation warnings for use of MimeTypeGuesser.

### [1.2.0] - 2019-11-12

Updated Imagine dependency version, to `^1.2`.

### [1.1.5] - 2019-09-27

Added [kyranb](https://github.com/czim/file-handling/commits?author=kyranb)'s ImageOptimizationStrategy.

### [1.1.4] - 2019-09-26

Added check for file extension for image auto-orient strategy to avoid trying to read EXIF data from formats that do not support it.

### [1.1.3] - 2019-07-21

Improved error handling in UrlDownloader.

### [1.1.2] - 2019-07-19

- FileHandler now returns a `ProcessResult` object for `process()` and `processSingleVariant()`. The result containts both the array of stored files as well as a list of temporary files created while processing.
- Added `getTemporaryFiles()` and `clearTemporaryFiles()` to the VariantProcessor.
- No longer marks copies as 'uploaded'.
- Added `delete()` to the StorableFileInterface and implementations. If you're upgrading, make sure to check your own implementations of this interface.
- Made exif errors silent (using the dreaded @) for now, *if* quiet mode is enabled.

### [1.0.4] - 2019-01-09

Improved the `StorableFileFactory`: now accepts StorableFile instances and better handles raw content.

### [1.0.3] - 2018-10-29

Fixed resizer to allow use of either `convertOptions` or `convert_options` in configuration options.


### [1.0.2] - 2018-08-02

Tweaked the downloader fix for better support.


### [1.0.1] - 2018-06-21

Fixed issue where downloader failed to work with URLs that contained spaces.


### [1.0.0] - 2018-05-13

Much improved path handling and flexibility.
There are *many* breaking changes here!
Please take care when updating, this will likely affect any code relying on this package.

- `Target` added to allow more flexible original and variant path handling.
    See for instance [czim/laravel-paperclip](https://github.com/czim/laravel-paperclip) for a target that interpolates placeholders.
- Changed `FileHandlerInterface`:
    - Altered signatures for `process()`, `processVariant()`, `delete()` and `deleteVariant()` to expected `TargetInterface` instead of string path.
    - Removed `variantUrlsForBasePath()` and `variantUrlsForStoredFile()`; replaced with new `variantUrlsForTarget()`.
- `FileHandler` constructor no longer takes a `PathHelperInterface` parameter.
- Removed `PathHelperInterface` and its implementation entirely.
- Removed `variantUrlsForStoredFile` and `variantUrlsForBasePath` from the `FileHandler`.
- Updated `StorageInterface` and `LaravelStorage` to expect a *full* path, including the filename, rather than only a directory.

[2.1.2]: https://github.com/czim/file-handling/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/czim/file-handling/compare/2.0.3...2.1.1
[2.0.3]: https://github.com/czim/file-handling/compare/2.0.2...2.0.3
[2.0.2]: https://github.com/czim/file-handling/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/czim/file-handling/compare/1.3.1...2.0.1

[1.3.1]: https://github.com/czim/file-handling/compare/1.2.1...1.3.1
[1.2.1]: https://github.com/czim/file-handling/compare/1.2.0...1.2.1
[1.2.0]: https://github.com/czim/file-handling/compare/1.1.5...1.2.0
[1.1.5]: https://github.com/czim/file-handling/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/czim/file-handling/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/czim/file-handling/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/czim/file-handling/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/czim/file-handling/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/czim/file-handling/compare/1.0.4...1.1.0
[1.0.4]: https://github.com/czim/file-handling/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/czim/file-handling/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/czim/file-handling/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/czim/file-handling/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/czim/file-handling/compare/0.9.10...1.0.0
