<?php
/**
 * Parses and verifies the doc comments for functions.
 *
 * This file is based on PEAR/Sniffs/Commenting/FunctionCommentSniff.php (commit: 23e8320). Changes were made under copyright
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
 * Parses and verifies the doc comments for functions.
 *
 * This class is based on PEAR/Sniffs/Commenting/FunctionCommentSniff (commit: 23e8320). Changes were made under copyright
 * by OXID eSales AG for use with special behaviour in OXID eShop.
 *   - Added special treatment for "@return" statements
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
class Oxid_Sniffs_Commenting_FunctionCommentSniff extends PEAR_Sniffs_Commenting_FunctionCommentSniff
{
    /**
     * Check it is a PHP Interface, but one time.
     *
     * @var array
     */
    protected $isFileInterface = array();

    /**
     * Process the return comment of this function comment.
     *
     * @param PHP_CodeSniffer_File $phpcsFile    The file being scanned.
     * @param int                  $stackPtr     The position of the current token
     *                                           in the stack passed in $tokens.
     * @param int                  $commentStart The position in the stack where the comment started.
     *
     * @return void
     */
    protected function processReturn(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $commentStart)
    {
        $tokens = $phpcsFile->getTokens();

        // Skip constructor and destructor.
        $methodName      = $phpcsFile->getDeclarationName($stackPtr);
        $isSpecialMethod = ($methodName === '__construct' || $methodName === '__destruct');

        $return = null;
        foreach ($tokens[$commentStart]['comment_tags'] as $tag) {
            if ($tokens[$tag]['content'] === '@return') {
                if ($return !== null) {
                    $error = 'Only 1 @return tag is allowed in a function comment';
                    $phpcsFile->addError($error, $tag, 'DuplicateReturn');
                    return;
                }

                $return = $tag;
            }
        }

        if ($this->isInterface($phpcsFile)) {
            return;
        }
        
        if ($isSpecialMethod === true) {
            return;
        }

        if ($return !== null) {
            $content = $tokens[($return + 2)]['content'];
            if (empty($content) === true || $tokens[($return + 2)]['code'] !== T_DOC_COMMENT_STRING) {
                $error = 'Return type missing for @return tag in function comment';
                $phpcsFile->addError($error, $return, 'MissingReturnType');
            } elseif (!$this->functionHasReturnStatement($phpcsFile, $stackPtr)) {
                $error = 'Function return type is set, but function has no return statement';
                $phpcsFile->addError($error, $return, 'InvalidNoReturn');
            }
        } elseif ($this->functionHasReturnStatement($phpcsFile, $stackPtr)) {
            $error = 'Missing @return tag in function comment.';
            $phpcsFile->addError($error, $tokens[$commentStart]['comment_closer'], 'MissingReturn');
        }//end if

    }//end processReturn()

    /**
     * Search if Function has a Return Statement
     *
     * @param PHP_CodeSniffer_File $phpcsFile      The file being scanned.
     * @param int                  $stackPtr       The position of the current token
     *                                             in the stack passed in $tokens.
     * @param int                  $continueSearch If found a return in Condition of a closure
     *                                             than continue to search
     *
     * @return bool
     */
    protected function functionHasReturnStatement(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $continueSearch = null)
    {
        $tokens = $phpcsFile->getTokens();
        $sFunctionToken = $tokens[$stackPtr];

        $returnStatement = false;
        if (isset($sFunctionToken['scope_closer']) and isset($sFunctionToken['scope_opener'])) {
            $startSearch = $continueSearch !== null ? $continueSearch : $sFunctionToken['scope_opener'];
            $returnStatement = $phpcsFile->findNext(T_RETURN, $startSearch, $sFunctionToken['scope_closer']);
        }

        if ($returnStatement !== false) {
            if ($phpcsFile->hasCondition($returnStatement, T_CLOSURE)) {
                return $this->functionHasReturnStatement($phpcsFile, $stackPtr, $returnStatement+1);
            } else {
                return true;
            }
        }

        return false;
    }//end functionHasReturnStatement()

    /**
     * Test if this a PHP Interface File
     *
     * @param PHP_CodeSniffer_File $phpcsFile
     *
     * @return bool
     */
    protected function isInterface(PHP_CodeSniffer_File $phpcsFile) {
        $checkFile = "". $phpcsFile->getFilename();
        if (isset($this->isFileInterface[$checkFile])) {
            return (bool) $this->isFileInterface[$checkFile];
        }

        $interface = $phpcsFile->findNext(T_INTERFACE, 0);

        return $this->isFileInterface[$checkFile] = (bool)(empty($interface) == false);
    }//end isInterfaceClass()

}//end class
