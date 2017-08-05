[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/czim/file-handling.svg?branch=master)](https://travis-ci.org/czim/file-handling)
[![Coverage Status](https://coveralls.io/repos/github/czim/file-handling/badge.svg?branch=master)](https://coveralls.io/github/czim/laravel-cms-upload-module?branch=master)


# File Handling and Storage Helper

Handles uploads, manipulations and (external) storage

## Usage

This package is framework-independent.

Here's an example of how to set up variant processing in general:

```php
<?php
    // Set up a storage implementation (for your framework of choice)
    /** @var \Czim\FileHandling\Contracts\Storage\StorageInterface $storage */
    $sourcePath = 'storage/input-file-name.jpg';
    

    // Source File
    $helper      = new \Czim\FileHandling\Support\Content\MimeTypeHelper;
    $interpreter = new \Czim\FileHandling\Support\Content\UploadedContentInterpreter;
    $downloader  = new Czim\FileHandling\Support\Download\UrlDownloader($helper);
    $factory     = new Czim\FileHandling\Storage\File\StorableFileFactory($helper, $interpreter, $downloader);

    $file = $factory->makeFromLocalPath($sourcePath);

    // Handler
    $strategyFactory = new Czim\FileHandling\Variant\VariantStrategyFactory;
    $strategyFactory->setConfig([
        'aliases' => [
            'resize'     => \Czim\FileHandling\Variant\Strategies\ImageResizeStrategy::class,
            'autoOrient' => \Czim\FileHandling\Variant\Strategies\ImageAutoOrientStrategy::class,
        ],
    ]);

    $processor = new Czim\FileHandling\Variant\VariantProcessor($factory, $strategyFactory);
    $pather    = new Czim\FileHandling\Storage\PathHelper;

    $handler = new Czim\FileHandling\Handler\FileHandler($storage, $processor, $pather);

    $handler->process($file, 'target/test-path', [
        'variants' => [
            'tiny' => [
                'autoOrient' => [],
                'resize' => [
                    'dimensions' => '30x30',
                ],
            ],
            'orient' => [
                'autoOrient' => [
                    'quiet' => false,
                ],
            ],
        ],
    ]);
``` 

For Laravel, you could use the following framework specific storage implementation:

```php
<?php
    // Storage
    $storage = new Czim\FileHandling\Storage\Laravel\LaravelStorage(
        \Storage::disk('testing'),
        'filesystems.disks.testing',
        true,
        url('testing')
    );
```

It is recommended of course to use the dependency container / IoC solution of your framework to simplify the above approach.



### Storage

Files can be stored using customizable storage implementations.

### Variants

When a file is processed, variants can be created automatically and stored along with the original.

These can be resizes, crops or recolors of the original image.
This package is set up to allow you to easily create your own strategies for making variants.

A single variant is defined by one or more strategy steps, making it possible to combine effects and re-use strategies.

Variants can be re-created from the original.

Included strategies:

For images:
- `ImageAutoOrientStrategy`: Re-orients rotated or flipped images.
- `ImageResizeStrategy`: Resizes (and crops) images.  
    (Uses Stapler's approach to resizes.)
    
For videos:
- `VideoScreenshotStrategy`: Extracts a video frame for a preview.  
    (Requires `ffmpeg`/`ffprobe`) **Not included yet, planned**

 
### Variant Gotcha

When using the resize strategy while working with potentially EXIF-rotated images, keep in mind that portrait/landscape width/height only resizes may run into trouble unless they are auto-oriented first.

For this reason, it is recommended to precede the `ImageResizeStrategy` by the `ImageAutoOrientStrategy`.

 
## Configuration

Configuration of file handling is set by injecting an associative array with a tree structure into the FileHandler.



## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/czim/file-handling.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/czim/file-handling.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/czim/file-handling
[link-downloads]: https://packagist.org/packages/czim/file-handling
[link-author]: https://github.com/czim
[link-contributors]: ../../contributors
