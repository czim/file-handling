# Security

## Reporting a Vulnerability

Please do not report security vulnerabilities through public GitHub issues, discussions or pull requests.

Instead, report them privately through GitHub:
[Report a vulnerability](https://github.com/czim/file-handling/security/advisories/new).

Please include:

- the affected version(s),
- a description of the issue and its impact,
- steps to reproduce, or a proof of concept,
- a suggested fix, if you have one.

You will receive a response as soon as possible. Once a fix is released, the vulnerability will be disclosed
through a GitHub Security Advisory, and a CVE will be requested where applicable. Reporters are credited unless they
prefer otherwise.


# Security Risks

If you used the `czim/file-handling` package before version `1.5.1` (for `1.*`) or `2.3.1` (for `2.*`), one or more of the following security risks were present:

- Before `1.5.0` and `2.3.0`: [Local Data Exposure](#local-data-exposure) and [Remote Data Access](#remote-data-access).
- Before `1.5.1` and `2.3.1`: TLS certificates were not verified when downloading remote files, allowing anyone on the network path to replace the downloaded content.
  See [Customizing cURL options for downloads](README.md#customizing-curl-options-for-downloads) for the new behaviour and how to adjust it.


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
