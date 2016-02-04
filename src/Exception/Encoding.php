<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Json\Exception;

/**
 * JSON 文字列にできない値が含まれている
 *
 * @package Json
 *
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 */
class Encoding extends \RuntimeException
{
    /**
     * @param \Json\LastError $error
     * @param \Exception|null $previous
     */
    public function __construct(\Json\LastError $error, \Exception $previous = null)
    {
        $message = $error->getMessage();
        $code    = $error->getCode();
        parent::__construct(sprintf('JSON encode failed: %s (%d)', $message, $code), $code, $previous);
    }
}
