# TBPixel/XMLStreamer

#### Content

- [Installation](#installation)
- [Purpose](#purpose)
  - [Examples](#examples)
  - [Automatic Casting](#automatic-casting)
- [Extending](#extending)
- [Contributing](#contributing)
- [Changelog](#changelog)
- [Support Me](#support-me)
- [License](#license)

## Installation

You can install this package via composer:

```bash
composer require tbpixel/xml-streamer
```

## Purpose

I found myself in need of a way to work with large XML data efficiently. The built in `XMLReader` PHP provides is fast and efficient, but can be a pain to work with at times. I wanted a dependency-free way to stream XML data and work with it using my provided classmap.

This package attempts to alleviate some of the headache of working with XMLReader, while also providing a collection of PSR-7 compatible XML streams. It offers a convenient way to iterate large XML data sets for reduced memory usage.

The optional client is also provided to allow for casting XML Strings to classmap objects.

### Examples

Say we had an XML file called `users.xml` with the following data:

```xml
<?xml version='1.0' encoding='UTF-8'?>
<users>
    <user>
        <id>1</id>
        <name>John Doe</name>
    </user>
    <user>
        <id>2</id>
        <name>Theodor</name>
    </user>
</users>
```

With this package, we can simply create a new *Client*, pass it a PSR-7 compatible stream, and work with our data using our types.

```php
$stream = new \TBPixel\XMLStreamer\Streams\FileReaderStream('users.xml');
$client = new \TBPixel\XMLStreamer\Client($stream);

foreach ($client->iterate() as $simpleXMLElement) {
    // Do something with the SimpleXMLElement
}

$client->close(); // Closes the client's provided stream
```

### Automatic Casting

If we had a user which implemented the required `CreateFromSimpleXML` interface, we could also cast the `SimpleXMLElement` as we iterate for easier access.

```php
use TBPixel\XMLStreamer\CreateFromSimpleXML;

class User implements CreateFromSimpleXML
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * Create a new instance from a Simple XML Element.
     *
     * @return static
     */
    public static function fromSimpleXML(\SimpleXMLElement $element)
    {
        return new static(
            (int) $element->id,
            (string) $element->name
        );
    }
}
```

Now when we create our client, lets just pass the FQCN to the clients classmap and casting will be automated.

```php
$stream = new \TBPixel\XMLStreamer\Streams\FileReaderStream('users.xml');
$client = new \TBPixel\XMLStreamer\Client($stream, [
    'user' => User::class,
]);

foreach ($client->iterate() as $user) {
    // Work with the User object.
}

$client->close();
```

The clients second argument is an array of key value pairs mapping the XML element names to the FQCN.

## Extending

Both the client and the streams are built ontop of PSR-7's `Psr\Http\Message\StreamInterface`, meaning it should be possible for you to swap out the stream with your own implementation. If you plan to use `XMLReader` but want a different resource handler, the `TBPixel\XMLStreamer\ReaderStream` is an abstract implementation which expects to process XML streams via XMLReader and handles most functionality well.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Support Me

Hi! I'm a developer living in Vancouver, BC and boy is the housing market tough. If you wanna support me, consider following me on [Twitter @TBPixel](https://twitter.com/TBPixel), or consider [buying me a coffee](https://ko-fi.com/tbpixel).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
