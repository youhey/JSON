<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Json;

/**
 * Wrapping for json_last_error()
 *
 * @package Json
 *
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 *
 * @see json_last_error()
 * @see json_last_error_msg()
 */
class LastError
{
    /** 下位互換のためのエラーメッセージ */
    const
        MESSAGE_ERROR_DEPTH          = 'Maximum stack depth exceeded',
        MESSAGE_ERROR_STATE_MISMATCH = 'Underflow or the modes mismatch',
        MESSAGE_ERROR_CTRL_CHAR      = 'Unexpected control character found',
        MESSAGE_ERROR_SYNTAX         = 'Syntax error',
        MESSAGE_ERROR_UTF8           = 'Malformed UTF-8 characters, possibly incorrectly encoded',
        MESSAGE_UNKNOWN_ERROR        = 'Unknown error';

    /** @var int last error occurred */
    private $error_code;

    /** @var string|null|false error string of the last occurred */
    private $error_message = false;

    /** constructor */
    public function __construct()
    {
        $this->error_code = json_last_error();

        if (function_exists('json_last_error_msg')) {
            $this->error_message = json_last_error_msg();
        }
    }

    /**
     * 直近の実行結果でエーラーが発生したか？
     *
     * @return bool エラーが発生していればTRUE、発生していなければFALSE
     */
    public function exists()
    {
        return ($this->error_code !== JSON_ERROR_NONE);
    }

    /**
     * 直近に実行した結果のエーラーコードを返却
     *
     * <dl>
     * <dt>JSON_ERROR_NONE</dt>
     * <dd>エラーは発生しませんでした</dd>
     * <dt>JSON_ERROR_DEPTH</dt>
     * <dd>スタックの深さの最大値を超えました</dd>
     * <dt>JSON_ERROR_STATE_MISMATCH</dt>
     * <dd>JSON の形式が無効、あるいは壊れています</dd>
     * <dt>JSON_ERROR_CTRL_CHAR</dt>
     * <dd>制御文字エラー。おそらくエンコーディングが違います</dd>
     * <dt>JSON_ERROR_SYNTAX</dt>
     * <dd>構文エラー</dd>
     * <dt>JSON_ERROR_UTF8</dt>
     * <dd>正しくエンコードされていないなど、不正な形式の UTF-8 文字（PHP 5.3.3）</dd>
     * <dt>JSON_ERROR_RECURSION</dt>
     * <dd>エンコード対象の値に再帰参照が含まれています（PHP 5.5.0）</dd>
     * <dt>JSON_ERROR_INF_OR_NAN</dt>
     * <dd>エンコード対象の値に NAN あるいは INF が含まれています。（PHP 5.5.0）</dd>
     * <dt>JSON_ERROR_UNSUPPORTED_TYPE</dt>
     * <dd>エンコード不可能な型の値が渡されました（PHP 5.5.0）</dd>
     * </dl>
     *
     * @return int
     */
    public function getCode()
    {
        return $this->error_code;
    }

    /**
     * 直近に実行した結果のエーラー文字列を返却
     *
     * @return string|null 直近の実行結果がエラーでなければNULL
     */
    public function getMessage()
    {
        if ($this->error_message !== false) {
            return $this->error_message;
        }

        switch ($this->error_code) {
            case JSON_ERROR_NONE :
                return null;
                break;
            case JSON_ERROR_DEPTH :
                return self::MESSAGE_ERROR_DEPTH;
                break;
            case JSON_ERROR_STATE_MISMATCH :
                return self::MESSAGE_ERROR_STATE_MISMATCH;
                break;
            case JSON_ERROR_CTRL_CHAR :
                return self::MESSAGE_ERROR_CTRL_CHAR;
                break;
            case JSON_ERROR_SYNTAX :
                return self::MESSAGE_ERROR_SYNTAX;
                break;
        }

        if (defined('JSON_ERROR_UTF8')) {
            if ($this->error_code === JSON_ERROR_UTF8) {
                return self::MESSAGE_ERROR_UTF8;
            }
        }

        return self::MESSAGE_UNKNOWN_ERROR;
    }
}
