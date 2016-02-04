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
class Decoding extends \RuntimeException
{
    /**
     * @param \Exception|null $previous
     * @return \Json\Exception\Decoding
     */
    public static function errorDepth(\Exception $previous = null)
    {
        return new self('JSON decode failed: Maximum stack depth exceeded (1)', JSON_ERROR_DEPTH, $previous);
    }
}
