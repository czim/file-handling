# Security Risks

If you used the `czim/file-handling` package before version `1.5.0` and `2.3.0`, the following security risks were present.


## Local Data Exposure

The following loads the contents from the indicated local file URI into a storable file record:

```php
/** @var \Czim\FileHandling\Storage\File\StorableFileFactory $factory */
// Loads content from the given URI:  
$file = $factory->makeFromUrl('file://../../.secrets');
// Also works:
$file = $factory->makeFromAny('file://../../.secrets');
``` 

Developers of dependents unaware of this feature might unintentionally open up their local files for processing and exposure.

In newer versions, this will instead create a storable file with the string data (the URI as a string) as its content instead.

To deliberately allow the old behavior, call the following method on the URL validator before processing:

```php
$validator = new \Czim\FileHandling\Support\Download\UriValidator();

// This will allow `file://...` type URIs to be processed as ways to import file contents from local files.
$validator->allowLocalUri();

$factory = new \Czim\FileHandling\Storage\File\StorableFileFactory(
    new \Czim\FileHandling\Support\Content\MimeTypeHelper,
    new \Czim\FileHandling\Support\Content\UploadedContentInterpreter,
    new \Czim\FileHandling\Support\Download\UrlDownloader(new \Czim\FileHandling\Support\Content\MimeTypeHelper),
    $validator
);
```

## Remote Data Access

The following loads the contents from the indicated remote URI source into a storable file record:

```php
/** @var \Czim\FileHandling\Storage\File\StorableFileFactory $factory */
// Loads content from the given URI:  
$file = $factory->makeFromUrl('https://github.com/czim/file-handling/blob/master/README.md');
// Also works:
$file = $factory->makeFromAny('https://github.com/czim/file-handling/blob/master/README.md');
``` 

Developers of dependents unaware of this feature might unintentionally allow input to dictate outgoing requests, opening up the possibility of [SSRF](https://owasp.org/www-community/attacks/Server_Side_Request_Forgery) attacks.

In newer versions, this will instead create a storable file with the string data (the URI as a string) as its content instead.

To deliberately allow the old behavior, call the following method on the URL validator before processing:

```php
$validator = new \Czim\FileHandling\Support\Download\UriValidator();

// This will allow `https://...` and `ftp://...` type URIs to be processed as ways to import file contents from remote locations.
$validator->allowRemoteUri();

$factory = new \Czim\FileHandling\Storage\File\StorableFileFactory(
    new \Czim\FileHandling\Support\Content\MimeTypeHelper,
    new \Czim\FileHandling\Support\Content\UploadedContentInterpreter,
    new \Czim\FileHandling\Support\Download\UrlDownloader(new \Czim\FileHandling\Support\Content\MimeTypeHelper),
    $validator
);
```
