<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
namespace Json;

/**
 * JSON
 *
 * <pre>
 * $json = <<<JSN
 * {
 *   "name": "Luke Skywalker",
 *   "teacher": "Yoda",
 *   "job": "Jedi",
 *   "force": true,
 *   "height": 1.72,
 *   "weight": 77,
 *   "droid": ["C-3PO","R2-D2"]
 * }
 * JSN;
 * $data = \Json\Json::parse($json);
 * </pre>
 * <pre>
 * $data = [
 *         'name'    => 'Luke Skywalker',
 *         'teacher' => 'Yoda',
 *         'job'     => 'Jedi',
 *         'force'   => true,
 *         'height'  => 1.72,
 *         'weight'  => 77,
 *         'droid'   => ['C-3PO', 'R2-D2'],
 *     ];
 * $json = \Json\Json::stringify($data);
 * </pre>
 *
 * @package Json
 *
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 *
 * @see json_decode()
 * @see json_encode()
 */
class Json
{
    /** 再帰の深さ */
    const DEFAULT_DEPTH_LIMIT = 512;

    /** @var int デコードで許容する再帰の深さ */
    public static $max_depth = self::DEFAULT_DEPTH_LIMIT;

    /**
     * json_encode options
     *
     * @var int
     *
     * @see json_decode()
     * @see JSON_UNESCAPED_SLASHES
     * @see JSON_UNESCAPED_UNICODE
     */
    static public $encode_option = 320;
    // $encode_option = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
    // - JSON_UNESCAPED_SLASHES => 64
    // - JSON_UNESCAPED_UNICODE => 256

    /**
     * json_decode options
     *
     * @var int
     *
     * @see json_encode()
     * @see JSON_BIGINT_AS_STRING
     */
    static public $decode_option = 2;
    // $encode_option = JSON_BIGINT_AS_STRING;
    // - JSON_BIGINT_AS_STRING => 2

    /**
     * JSON形式の文字列をデコード
     *
     * <p>UTF-8のみBOMを無視するよう対応<br>
     * PHPのバージョンが5.4.0以上であれば以下の動作に対応</p>
     * <dl>
     * <dt>JSON_BIGINT_AS_STRING</dt>
     * <dd>大きな整数値を文字列としてエンコード（公式ドキュメントまま）</dd>
     * </dl>
     *
     * @param string $json デコードするJSON形式の文字列
     * @param bool $assoc オブジェクトを連想配列で返すか？
     *
     * @return mixed デコードした結果
     *
     * @throws \Json\Exception\Decoding JSONデータが不正
     * @throws \Json\Exception\SyntaxError JSONデータが不正
     *
     * @see json_decode()
     */
    public static function parse($json, $assoc = false)
    {
        // Strip off BOM (UTF-8)
        if (strpos($json, "\xef\xbb\xbf") === 0) {
            $json = substr($json, 3);
        }

        // 性能に影響のない範囲で単純なデコードのみ自前対応
        if ($json === '') {
            return null;
        }
        if ($json === 'null') {
            return null;
        }
        if ($json === 'true') {
            return true;
        }
        if ($json === 'false') {
            return false;
        }
        if ($json === '{}') {
            if ($assoc) {
                return array();
            }
            return new \stdClass;
        }
        if ($json === '[]') {
            return array();
        }
        if ($json === '""') {
            return '';
        }

        if (static::isSupportedOptions()) {
            $decoded = @json_decode($json, $assoc, self::$max_depth, self::$decode_option);
        } else {
            $decoded = @json_decode($json, $assoc);
        }

        if (!is_null($decoded)) {
            return $decoded;
        }

        $last_error = new LastError;
        if (!$last_error->exists()) {
            return $decoded;
        }

        if ($last_error->getCode() === JSON_ERROR_DEPTH) {
            throw \Json\Exception\Decoding::errorDepth();
        }

        throw new \Json\Exception\SyntaxError($last_error);
    }

    /**
     * PHPの値をJSON形式の文字列に変換
     *
     * <p>PHPのバージョンが5.4.0以上であれば以下の動作に対応</p>
     * <dl>
     * <dt>JSON_UNESCAPED_SLASHES</dt>
     * <dd>"/" をエスケープしない</dd>
     * <dt>JSON_UNESCAPED_UNICODE</dt>
     * <dd>マルチバイト Unicode 文字をそのままの形式で扱う</dd>
     * </dl>
     *
     * @param mixed $data エンコードする値
     *
     * @return string 値をエンコードしたJSON形式の文字列
     *
     * @throws \Json\Exception\Encoding エンコードできないデータが含まれている
     *
     * @see json_encode()
     */
    public static function stringify($data)
    {
        // 性能に影響のない範囲で単純なエンコードのみ自前対応
        if (is_null($data)) {
            return 'null';
        }
        if ($data === true) {
            return 'true';
        }
        if ($data === false) {
            return 'false';
        }
        if ($data === array()) {
            return '[]';
        }
        if ($data === '') {
            return '""';
        }

        if (static::isSupportedOptions()) {
            $encoded = @json_encode($data, self::$encode_option);
        } else {
            $encoded = @json_encode($data);
        }

        if ($encoded !== false) {
            return $encoded;
        }

        $last_error = new LastError;
        if (!$last_error->exists()) {
            return $encoded;
        }

        throw new \Json\Exception\Encoding($last_error);
    }

    protected static function isSupportedOptions()
    {
        static $supported_options = null;

        if (is_null($supported_options)) {
            $supported_options = version_compare(PHP_VERSION, '5.4.0', '>=');
        }

        return $supported_options;
    }
}
