[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status](https://travis-ci.org/czim/file-handling.svg?branch=master)](https://travis-ci.org/czim/file-handling)
[![Coverage Status](https://coveralls.io/repos/github/czim/file-handling/badge.svg?branch=master)](https://coveralls.io/github/czim/file-handling?branch=master)


# File Handling and Storage Helper

Handles uploads, manipulations and (external) storage


## Changelog

[View the changelog](CHANGELOG.md).


## Usage

This package is framework-independent.

Here's an example of how to set up variant processing in general:

```php
<?php
    // Set up a storage implementation (for your framework of choice), and a PSR-11 container implementation.
    /** @var \Czim\FileHandling\Contracts\Storage\StorageInterface $storage */
    /** @var \Psr\Container\ContainerInterface $container */
    
    $sourcePath = 'storage/input-file-name.jpg';
    

    // Source File
    $helper      = new \Czim\FileHandling\Support\Content\MimeTypeHelper;
    $interpreter = new \Czim\FileHandling\Support\Content\UploadedContentInterpreter;
    $downloader  = new \Czim\FileHandling\Support\Download\UrlDownloader($helper);
    $factory     = new \Czim\FileHandling\Storage\File\StorableFileFactory($helper, $interpreter, $downloader);

    $file = $factory->makeFromLocalPath($sourcePath);

    // Handler
    $strategyFactory = new \Czim\FileHandling\Variant\VariantStrategyFactory($container);
    $strategyFactory->setConfig([
        'aliases' => [
            'resize'     => \Czim\FileHandling\Variant\Strategies\ImageResizeStrategy::class,
            'autoOrient' => \Czim\FileHandling\Variant\Strategies\ImageAutoOrientStrategy::class,
        ],
    ]);

    $processor = new \Czim\FileHandling\Variant\VariantProcessor($factory, $strategyFactory);

    $handler = new \Czim\FileHandling\Handler\FileHandler($storage, $processor);
    
    $handler->process($file, new Target($file->path()), [
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
    $storage = new \Czim\FileHandling\Storage\Laravel\LaravelStorage(
        \Storage::disk('testing'),
        true,
        url('testing')
    );
   
    // If you're using a Laravel version that does not have a PSR-11 compliant container yet:
    $container = new \Czim\FileHandling\Support\Container\LaravelContainerDecorator(app());
    
    app()->bind(\Imagine\Image\ImagineInterface::class, \Imagine\Gd\Imagine::class);
```

It is recommended of course to use the dependency container / IoC solution of your framework to simplify the above approach.


### Custom Container 

If you don't have a feasible PSR-11 container available, you can use a very simple implementation provided with this package.

```php
<?php
    $container = new \Czim\FileHandling\Support\Container\SimpleContainer;
    
    $container->registerInstance(
        \Czim\FileHandling\Variant\Strategies\ImageResizeStrategy::class,
        new \Czim\FileHandling\Variant\Strategies\ImageResizeStrategy(
            new \Czim\FileHandling\Support\Image\Resizer(
                new \Imagine\Gd\Imagine
            )
        )
    );
    $container->registerInstance(
        \Czim\FileHandling\Variant\Strategies\ImageAutoOrientStrategy::class,
        new \Czim\FileHandling\Variant\Strategies\ImageAutoOrientStrategy(
                new \Czim\FileHandling\Support\Image\OrientationFixer(new \Imagine\Gd\Imagine)
            )
    );
```


### Storage

Files can be stored using customizable storage implementations.

A very simple adapter/decorator for the Laravel storage is provided.
For any other framework/setup you will (for now) have to write your own implementation of the `\Czim\FileHandling\Contracts\Storage\StorageInterface`.  


### Variants

When a file is processed, variants can be created automatically and stored along with the original.

These can be resizes, crops or recolors of the original image.
This package is set up to allow you to easily create your own strategies for making variants.

A single variant is defined by one or more strategy steps, making it possible to combine effects and re-use strategies.

Variants can be re-created from the original.


Note that it this package is designed to easily create and add in custom variant strategies. Consider the source code for the strategies listed below examples to get you started.


#### Image Strategies

Included strategies for image manipulation:

- `ImageAutoOrientStrategy`: Re-orients rotated or flipped images.

- `ImageResizeStrategy`: Resizes (and crops) images.  
    (Uses Stapler's options & approach to resizes.)

- `ImageWatermarkStrategy`: Pastes a watermark onto an image.  
    In any corner or the center.  
    Options:  
    `position` (string): `top-left`, `top-right`, `center`, `bottom-left`, `bottom-right`.  
    `watermark` (string): full path to the watermark image.  
    The watermark should be a PNG (transparent) image for best results. 

- `ImageOptimizationStrategy` Optimizes images to decreate their file size.

    This strategy requires installation of [spatie/image-optimizer](https://github.com/spatie/image-optimizer).
    
    In order for it to work, you'll need to install a few image optimizers as well:
    - `jpegoptim`
    - `optipng`
    - `pngquant`
    - `svgo`
    - `gifsicle`
   
    Installation example for Ubuntu:

    ```bash
    sudo apt-get install jpegoptim optipng pngquant gifsicle
    sudo npm install -g svgo
    ```

    Installation example for MacOS, using [Homebrew](https://brew.sh):

    ```bash
    brew install jpegoptim optipng pngquant svgo gifsicle
    ```



#### Video Strategies

Included strategies for video manipulation:

- `VideoScreenshotStrategy`: Extracts a video frame for a preview.  
    (Requires `ffmpeg`/`ffprobe`).  
    Options:  
    `seconds` (int): the second of video runtime to take the shot at.  
    `percentage` (int): the percentage of video runtime to take the shot at (overruled by `seconds`).
    `ffmpeg` (string): path to ffmpeg binary (if not `/usr/bin/ffmpeg`).
    `ffprobe` (string): path to ffprobe binary (if not `/usr/bin/ffprobe`).  

 
### Variant Gotcha

When using the resize strategy while working with potentially EXIF-rotated images, keep in mind that portrait/landscape width/height only resizes may run into trouble unless they are auto-oriented first.

For this reason, it is recommended to precede the `ImageResizeStrategy` by the `ImageAutoOrientStrategy`.

In general, it is always a good idea to consider the order in which strategies are applied.

You may also opt to combine multiple strategies into one strategy class, if efficiency is important. You may use this approach to prevent opening/saving the same file more than once.

 
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
