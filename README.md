OXID Coding Standards
=====================

This repository includes OXID eShop coding standards definition for PHP Codesniffer along with a PHPStorm autoformat config file. The PHP_CodeSniffer standard will never be 100% accurate, but should be viewed as a strong set of guidelines while contributing to OXID eShop.

See OXID eShop coding standards documentation at http://wiki.oxidforge.org/Coding_standards

##Requirements
* Latest Composer version

## Installation

Coding Standards setup uses composer to get required packages, so make sure to have composer installed and accessible. 
You can find composer installation guide [here](https://getcomposer.org/download/).

### Selecting where to install coding standards

Coding Standards can be installed directly within shop or to any other directory. 
However, installation varies slightly depending on selected location. We advise to install it using shop directory. 
#### Option 1: Selecting shop directory for installation (preferred way)

To install Coding Standards within shop directory, update/create `composer.json` with following values:
```
{
    "name": "oxid-esales/eshop",
    "description": "OXID eShop",
    "type": "project",
    "keywords": ["oxid", "modules", "eShop"],
    "homepage": "https://www.oxid-esales.com/en/home.html",
    "license": [
        "GPL-3.0",
        "proprietary"
    ],
    "repositories": {
        "oxid-esales/coding-standards": {
            "type": "vcs",
            "url": "https://github.com/OXID-eSales/coding_standards.git"
        },
        "squizlabs/php_codesniffer": {
            "type": "vcs",
            "url": "https://github.com/OXID-eSales/PHP_CodeSniffer.git"
        }
    },
    "require-dev": {
        "oxid-esales/coding-standards": "^2.0.0"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```
Installing this way, you can use `phpcsoxid` binary to check your shop directory for standards errors by `shop/path/vendor/bin/phpcsoxid`.
Latest development shop version already includes composer.json file in its source, so no changes needs to be made.

#### Option 2: Selecting any directory for installation (alternative way)

To install Coding Standards to any directory, you need to checkout Coding Standards from Github into desired directory (`git clone https://github.com/OXID-eSales/coding_standards.git`). Installing this way, binaries will be accessible from `coding_standards/bin`. You must manually set what directory you want to check for conding standards errors by giving directory path as argument for oxid phpcs binary, like: `phpcsoxid /path/to/my/shop/`.

### Installing Coding Standards

After you selected where you want to install the Coding Standards, follow these steps:

1. Navigate to the directory that you picked for installation.
1. Use composer to setup Coding Standards components (`composer install`). Ensure you do this from within the directory where `composer.json` is located. 


## Running Coding Standards checks

To run coding standards checkings, use phpcsoxid binary by:  
`phpcsoxid` - run phpcs with preconfigured Oxid standard, and show list of errors.  
`phpcsoxid /some/other/path` - run phpcs with preconfigured Oxid standard on some specific directory  
*(You must always provide directory for checking if coding standards was not installed by "Option 1" installation method.)*

It is possible to use standard codesniffer(phpcs) options too, like:  
`phpcsoxid --report=summary`

Alternatively, it is possible to use original phpcs, but you must configure it by yourself. As example:  
`phpcs --standard=/path/to/Standard/directory/Oxid/ /path/to/directory/for/checking/standards`
