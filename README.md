OXID Coding Standards
=====================

This repository includes OXID eShop coding standards definition for PHP Codesniffer along with a PHPStorm autoformat config file. The PHP_CodeSniffer standard will never be 100% accurate, but should be viewed as a strong set of guidelines while contributing to OXID eShop.

See OXID eShop coding standards documentation at http://wiki.oxidforge.org/Coding_standards

##Requirements
* PHP 5.3+
* PHP Codesniffer 1.5+

##Installation
Installation is as easy as checking out the repository to the correct location within PHP_CodeSniffer's directory structure.

###Install PHP_CodeSniffer

https://github.com/squizlabs/PHP_CodeSniffer#installation

###Install the OXID eShop standard

```git clone https://github.com/OXID-eSales/coding_standards.git /path/to/CodeSniffer/Standards/Oxid```

##Running

You can use the installed OXID eShop standard like:

```phpcs --standard=Oxid path/to/code```

Alternatively if it isn't installed you can still reference it by path like:

```phpcs --standard=path/to/oxid/coding-standards path/to/code```
