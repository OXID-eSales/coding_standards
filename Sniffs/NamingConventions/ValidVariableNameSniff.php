<?php
/**
 * Oxid_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Tobias Matthaiou <matthaiou@solutiondrive.de>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Oxid_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * Checks the naming of variables and member variables of right
 * Convions
 *
 * @category PHP
 * @package  PHP_CodeSniffer
 * @author   Tobias Matthaiou <tm@solutiondrive.de>
 * @license  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @version  Release: @package_version@
 * @link     http://pear.php.net/package/PHP_CodeSniffer
 */
class Oxid_Sniffs_NamingConventions_ValidVariableNameSniff extends Zend_Sniffs_NamingConventions_ValidVariableNameSniff
{

    /**
     * A list of hungarian Notation.
     *
     * @var string is used in a RegEx-Pattern
     */
    private $_hungarianNotation = "(a|bl|o|s|i|f|d|h|fn|m|is|my|db|fld)";

    /**
     * A list of reserved var, then its ok.
     *
     * @var array
     */
    protected $ignoreVaribaleNames = array(
        //PHP reserved var
                                            '_SERVER',
                                            '_GET',
                                            '_POST',
                                            '_REQUEST',
                                            '_SESSION',
                                            '_ENV',
                                            '_COOKIE',
                                            '_FILES',
                                            'GLOBALS',
                                            'http_response_header',
                                            'HTTP_RAW_POST_DATA',
                                            'php_errormsg',
                                            'this',
        //OXID reserved var
                                            'name',
                                            'key',
                                            'value',
                                            'fields',
                                            'fldtype',
    );

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens  = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');

        // If it's a php reserved var, then its ok.
        if (in_array($varName, $this->ignoreVaribaleNames) === true) {
            return;
        }

        $this->checkHungarianNotation($phpcsFile, $stackPtr, $varName, 'NotTypeName');

        parent::processVariable($phpcsFile, $stackPtr);
    }//end processVariable()


    /**
     * Processes class member variables.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens      = $phpcsFile->getTokens();
        $varName     = ltrim($tokens[$stackPtr]['content'], '$');

        $this->checkHungarianNotation($phpcsFile, $stackPtr, $varName, 'MemberVarNotTypeName');

        parent::processMemberVar($phpcsFile, $stackPtr);

    }//end processMemberVar()


    /**
     * Processes the variable found within a double quoted string.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the double quoted
     *                                        string.
     *
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (preg_match_all('|[^\\\]\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)|', $tokens[$stackPtr]['content'], $matches) !== 0) {
            foreach ($matches[1] as $varName) {
                // If it's a php reserved var, then its ok.
                if (in_array($varName, $this->ignoreVaribaleNames) === true) {
                    continue;
                }

                $this->checkHungarianNotation($phpcsFile, $stackPtr, $varName, 'StringVarNotTypeName');
            }//end foreach
        }//end if

        parent::processVariableInString($phpcsFile, $stackPtr);
    }//end processVariableInString()

    /**
     * Check the Variable Name of Hungarian Notation.
     *
     * @param PHP_CodeSniffer_File $phpcsFile           The file being scanned.
     * @param int                  $stackPtr            The position of the current token in the
     *                                                  stack passed in $tokens.
     * @param string               $varName             The name of Variable
     * @param string               $code                A violation code unique to the sniff message.
     * @param int|bool             $recordsWarningLevel Set the phpcf-Records as warning.
     *                                                  The severity level for this warning.
     *                                                  (Optional) default is Error-Records
     *
     * @return void
     */
    protected function checkHungarianNotation(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $varName, $code, $recordsWarningLevel = false)
    {
        if (!preg_match("/^_?{$this->_hungarianNotation}[A-Z]/", $varName)) {
            $error = $this->getErrorMsg($code);
            $data  = array($varName);
            if ($recordsWarningLevel !== false) {
                $phpcsFile->addWarningOnLine($error, $stackPtr, $code, $data, $recordsWarningLevel);
            } else {
                $phpcsFile->addError($error, $stackPtr, $code, $data);
            }
        }

        $this->subVariablename($phpcsFile, $stackPtr);
    }//end checkHungarianNotation

    /**
     * Check Variable after object operator (->).
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens
     *
     * @return void
     */
    protected function subVariablename(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        //is Object
        if (!isset($tokens[$stackPtr+1]) || $tokens[$stackPtr+1]['code'] != T_OBJECT_OPERATOR) {
            return;
        }
        //is Variable Name
        if (!isset($tokens[$stackPtr+2]) || $tokens[$stackPtr+2]['code'] != T_STRING) {
            return;
        }

        $varName = $tokens[$stackPtr+2]['content'];

        // If it's a reserved var, then its ok.
        if (in_array($varName, $this->ignoreVaribaleNames) === true) {
            return;
        }
        //is Function
        if (!isset($tokens[$stackPtr+3]) || $tokens[$stackPtr+3]['code'] == T_OPEN_PARENTHESIS) {
            return;
        }

        $this->checkHungarianNotation($phpcsFile, $stackPtr+2, $varName, 'SubVarNotTypeName', 0);
    }//end subVariablename

    /**
     * Get the Error Messages of a unique sniff
     *
     * @param string $code A violation code unique to the sniff message.
     *
     * @return string
     */
    protected function getErrorMsg($code)
    {
        $errorMsg = 'Variable "%s" start not with a Type character';
        switch ($code) {
            case 'SubVarNotTypeName':
                $errorMsg = 'Object variable "%s" start not with a Type character';
                break;
            case 'MemberVarNotTypeName':
                $errorMsg = 'Class Member variable "%s" start not with a Type character';
                break;
            case 'StringVarNotTypeName':
                $errorMsg = 'Variable in String "%s" start not with a Type character';
                break;
        }

        return $errorMsg;
    }//end getErrorMsg
}//end class
