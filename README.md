# Classic Fields Extension for Symphony CMS

-   Version: 1.0.0
-   Date: March 29 2020
-   [Release notes](https://github.com/pointybeard/classicfields/blob/master/CHANGELOG.md)
-   [GitHub repository](https://github.com/pointybeard/classicfields)

Collection of the original SymphonyCMS fields with the ability to enable, disable, install, and uninstall specific fields as required. Includes the original Date, Input, Textarea, Select, Taglist, Author, Checkbox, and Upload fields that came with an original Symphony install.

## Installation

This is an extension for Symphony CMS. Add it to your `/extensions` folder in your Symphony CMS installation, run `composer update` to install required packages and then enable it though the interface.

### Requirements

This extension requires PHP 7.3 or greater.

The [Console Extension for Symphony CMS](https://github.com/pointybeard/console) must also be installed.

This extension depends on the following Composer libraries:

-   [PHP Helpers](https://github.com/pointybeard/helpers)

### Setup

1. Run `composer update` on the `extension/classicfields` directory to install these.
2. Ensure that you have installed the [Console Extension for Symphony CMS](https://github.com/pointybeard/console). Run the following command inside your `extensions/` folder: `git clone https://github.com/pointybeard/console.git` and then observe the installation instructions in `console/README.md`.
3. Make `extensions/classicfields/fields` writable, e.g. `chmod 0777 extensions/classicfields/fields`. When a field is enabled or installed, a symbolic link to the actual field is generated here.
4. (optional) Make `symphony/lib/toolkit/fields` writable so this extension manage adding and removing symbolic links. This is necessary if you are using the [2.7.10-extended](https://github.com/pointybeard/symphony-2/tree/2.7.10-extended) or [2.7.10-extended-essentials](https://github.com/pointybeard/symphony-2/tree/2.7.10-extended-essentials) branch of Symphony.

## Usage

To enable, disable, install, or uninstall a Classic Field, use the same named command on the console like so: `symphony classicfields -t <token> <action> <target-field>`

For example:

```
symphony classicfields -t 123456 enable select
symphony classicfields -vvv -t 123456 uninstall input
```

See options available by adding `--help` after a command

```
symphony classicfields input --help
```

## Support

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/pointybeard/classicfields/issues),
or better yet, fork the library and submit a pull request.

## Contributing

We encourage you to contribute to this project. Please check out the [Contributing documentation](https://github.com/pointybeard/classicfields/blob/master/CONTRIBUTING.md) for guidelines about how to get involved.

## License

"Classic Fields Extension for Symphony CMS" is released under the [MIT License](http://www.opensource.org/licenses/MIT).
