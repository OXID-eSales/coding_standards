<?php
/**
 * Oxid_Sniffs_Functions_ForbiddenFunctionsSniff, based on Squiz code.
 *
 * This file is based on Generic/Sniffs/PHP/ForbiddenFunctionsSniff.php. Changes were made under copyright
 * by OXID eSales AG for use with special behaviour in OXID eShop.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Oxid_Sniffs_Functions_ForbiddenFunctionsSniff, based on Squiz code.
 *
 * Discourages the use of alias functions that are kept in PHP for compatibility
 * with older versions. Can be used to forbid the use of any function.
 *
 * This class is based on Generic/Sniffs/PHP/ForbiddenFunctionsSniff. Changes were made under copyright
 * by OXID eSales AG for use with special behaviour in OXID eShop:
 *   - Added var_dump to forbidden functions pool
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class Oxid_Sniffs_Functions_ForbiddenFunctionsSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{
    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is NULL if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    public $forbiddenFunctions = array(
        'var_dump'   => null,
        'sizeof'     => 'count',
        'delete'     => 'unset',
    );
}
