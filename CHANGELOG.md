# Changelog

All notable changes to `tbpixel/xml-streamer` will be documented in this file.

## 1.5.3 - 2019-03-14

- Provided Client always rewinds stream at end of iteration; resolves #20.

## 1.5.2 - 2019-03-12

- Re-addresses issue #21, resolving it properly this time.

## 1.5.1 - 2019-03-11

- Resolves an issue where ReaderStream did not iterate every record correctly; fixes #21.

## 1.5.0 - 2019-03-11

- Allows the client to return an array of items, resolving #17.

## 1.4.0 - 2019-03-11

- Allows the client to be countable, resolving #16.

## 1.3.0 - 2019-03-11

- Implements mutation testing library `infection`.
- Fixes an issue where the cursor was not wrapping correctly and adds tests.
- Fixes an issue where the `XMLReader` based streams did not properly rewind.
- Fixes an issue where `__toString` may throw an exception.
- Fixes an issue where iterating by tag name only returned the first result.

## 1.2.0 - 2019-03-05

- Resolve issue #12, implementing IteratorAggregate onto the client to allow for convenient looping.

## 1.1.2 - 2019-02-14

- Resolves issue #8, fixing stream size calculation and adjusting tests accordingly.

## 1.1.1 - 2019-02-13

- Resolves issue #6, adding `__destruct` magic method to ReaderStream to close stream on object destruct.

## 1.1.0 - 2019-02-12

- Resolves issue #3, adding the ability to pass a tag name to a reader stream and get it to iterate manually.

## 1.0.1 - 2019-02-07

- Resolves issue #1, a bug in which stream iteration depth was not equal to required set depth.

## 1.0.0 - 2019-02-06

- Initial release
