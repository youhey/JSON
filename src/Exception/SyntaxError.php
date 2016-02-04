<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Json\Exception;

/**
 * JSON 文字列の構文が有効ではない
 *
 * @package Json
 *
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 */
class SyntaxError extends Decoding
{
    /**
     * @param \Json\LastError $error
     * @param \Exception|null $previous
     */
    public function __construct(\Json\LastError $error, \Exception $previous = null)
    {
        $message = $error->getMessage();
        $code    = $error->getCode();
        parent::__construct(sprintf('JSON decode failed: %s (%d)', $message, $code), $code, $previous);
    }
}
