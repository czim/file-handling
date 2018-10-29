# Changelog

## 1.*

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


[1.0.3]: https://github.com/czim/file-handling/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/czim/file-handling/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/czim/file-handling/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/czim/file-handling/compare/0.9.10...1.0.0
