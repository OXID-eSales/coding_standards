<?php
/**
 * Parses and verifies the doc comments for functions.
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
 * Parses and verifies the doc comments for functions. Extension done by OXID.
 *
 * Verifies that :
 * <ul>
 *  <li>If function return type is set, return statement is present aswell</li>
 *  <li>If function has a return statement, expect @return tag</li>
 *  <li>If function does not return anything, don't expect return statement</li>
 *  <li>Return tag is not empty</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version   Release: 1.5.3
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 *
 */
class Oxid_Sniffs_Commenting_FunctionCommentSniff extends PEAR_Sniffs_Commenting_FunctionCommentSniff
{
    /**
     * The name of the method that we are currently processing.
     *
     * @var string
     */
    private $_sMethodName = '';

    /**
     * The position in the stack where the function token was found.
     *
     * @var int
     */
    private $_sFunctionToken = null;

    /**
     * The position in the stack where the class token was found.
     *
     * @var int
     */
    private $_sClassToken = null;




    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $oPhpCsFile The file being scanned.
     * @param int                  $iStackPtr  The position of the current token
     *                                         in the stack passed in $tokens.
     */
    public function process(PHP_CodeSniffer_File $oPhpCsFile, $iStackPtr)
    {
        $this->_sMethodName = $oPhpCsFile->getDeclarationName($iStackPtr);
        $this->_sFunctionToken = $iStackPtr;
        $aTokens            = $oPhpCsFile->getTokens();
        $this->_sClassToken = null;
        foreach ($aTokens[$iStackPtr]['conditions'] as $iCondPtr => $iCondition) {
            if ($iCondition === T_CLASS || $iCondition === T_INTERFACE) {
                $this->_sClassToken = $iCondPtr;
            }
        }
        parent::process($oPhpCsFile, $iStackPtr);
    }

    /**
     * Process return statement
     *
     * @param int $iCommentStart The position in the stack where the comment started.
     * @param int $iCommentEnd   The position in the stack where the comment ended.
     *
     * @return null
     */
    protected function processReturn($iCommentStart, $iCommentEnd)
    {
        $sRawContent = null;
        if ($this->_isConstructor()) {
            return;
        }

        $aTokens           = $this->currentFile->getTokens();
        /** @var PHP_CodeSniffer_CommentParser_PairElement $oReturn */
        if (!is_null($oReturn = $this->commentParser->getReturn())) {
            $sRawContent = trim($oReturn->getRawContent());
        }

        if (isset($aTokens[$this->_sFunctionToken]['scope_closer'])) {
            $iEndToken = $aTokens[$this->_sFunctionToken]['scope_closer'];
        }
        if ($oReturn === null) {
            if (isset($iEndToken) && $this->currentFile->findNext(T_RETURN, $this->_sFunctionToken, $iEndToken) !== false) {
                $iErrorPos = $iCommentEnd;
                $sError = 'Function returns some value, but function has no return type';
                $this->currentFile->addError($sError, $iErrorPos, 'InvalidReturnNotVoid');
            }
        } else {
            $iErrorPos = ($iCommentStart + $oReturn->getLine());
            if ($sRawContent === '') {
                $sError = '@return tag is empty in function comment';
                $this->currentFile->addError($sError, $iErrorPos, 'EmptyReturn');
            } elseif (!empty($sRawContent) && isset($iEndToken) && $sRawContent !== 'null'
                      && $this->currentFile->findNext(T_RETURN, $this->_sFunctionToken, $iEndToken) === false) {
                $sError = 'Function return type is set, but function has no return statement';
                $this->currentFile->addError($sError, $iErrorPos, 'InvalidNoReturn');
            }
        }


    }//end processReturn()


    /**
     * Check whether we are dealing with constructor method
     * Returns true if method is constructor, false otherwise
     *
     * @return bool
     */
    protected function _isConstructor()
    {
        $sClassName  = '';

        if ($this->_sClassToken !== null) {
            $sClassName = $this->currentFile->getDeclarationName($this->_sClassToken);
            $sClassName = strtolower(ltrim($sClassName, '_'));
        }

        $sMethodName       = strtolower(ltrim($this->_sMethodName, '_'));
        $blIsSpecialMethod = ($this->_sMethodName === '__construct' || $this->_sMethodName === '__destruct');
        // if we want constructor to be checked with @return checks, comment out this check
        if ($blIsSpecialMethod === true || $sMethodName === $sClassName) {
            return true;
        }
        return false;
    }
}
