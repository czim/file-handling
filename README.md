# File Handling and Storage Helper

Handles uploads, manipulations and (external) storage

## Usage

This package is framework-independent. 


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
    (Requires `ffmpeg`/`ffprobe`.)
 

 
## Configuration

Configuration of file handling is set by injecting an associative array with a tree structure into the FileHandler.



## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/czim/laravel-cms-media-module.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/czim/laravel-cms-media-module.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/czim/laravel-cms-media-module
[link-downloads]: https://packagist.org/packages/czim/laravel-cms-media-module
[link-author]: https://github.com/czim
[link-contributors]: ../../contributors
