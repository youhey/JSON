<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Json;

// get an object
class sample {}

/**
 * @package Json
 *
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 */
class EncodingTest extends \PHPUnit_Framework_TestCase
{
    // \Json\Json::stringify() {{{

    /**
     * @test
     * @dataProvider basicProvider
     */
    public function stringify($input, $expected)
    {
        $result = Json::stringify($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider realEncodingProvider
     */
    public function stringifyRealEncoding($input, $expected)
    {
        $result = Json::stringify($input);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function stringifyUtf8_OldPHP()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $this->markTestSkipped('PHP Version >= 5.4.0');
        }

        $input = base64_decode('5pel5pys6Kqe44OG44Kt44K544OI44Gn44GZ44CCMDEyMzTvvJXvvJbvvJfvvJjvvJnjgII=');

        $result   = Json::stringify($input);
        $expected = '"\u65e5\u672c\u8a9e\u30c6\u30ad\u30b9\u30c8\u3067\u3059\u300201234\uff15\uff16\uff17\uff18\uff19\u3002"';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function stringifyUtf8()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('PHP Version < 5.4.0');
        }

        $input = base64_decode('5pel5pys6Kqe44OG44Kt44K544OI44Gn44GZ44CCMDEyMzTvvJXvvJbvvJfvvJjvvJnjgII=');

        $result   = Json::stringify($input);
        $expected = "\"{$input}\"";
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function stringifyUnescapedSlashes()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('PHP Version < 5.4.0');
        }

        $input = 'a/b';

        $result   = Json::stringify($input);
        $expected = '"a/b"';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function stringifyUnescapedUnicode()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('PHP Version < 5.4.0');
        }

        $input = "latin 1234 -    russian мама мыла раму  specialchars \x02   \x08 \n   U+1D11E >&#119070;<";

        $result   = Json::stringify($input);
        $expected = '"latin 1234 -    russian мама мыла раму  specialchars \u0002   \b \n   U+1D11E >&#119070;<"';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function stringifyAnonymousFunction()
    {
        $result   = Json::stringify(function() { return null; });
        $expected = '{}';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @expectedException \Json\Exception\Encoding
     * @expectedExceptionMessage JSON encode failed: Maximum stack depth exceeded (1)
     */
    public function stringifyEncodeError()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $this->markTestSkipped('PHP Version < 5.5.0');
        }

        // Default max-depth=512 ( x > 512 -> JSON_ERROR_DEPTH )
        $input = array();
        for ($i = 0; $i < 513; $i++) {
            $input = array($input);
        }

        $ignore = Json::stringify($input);
    }

    /**
     * @test
     * @expectedException \Json\Exception\Encoding
     * @expectedExceptionMessage JSON encode failed: Type is not supported (8)
     */
    public function failEncodeFp()
    {
        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            $this->markTestSkipped('PHP Version < 5.5.0');
        }

        // get a resource variable
        $fp = fopen(__FILE__, "r");

        $ignore = Json::stringify($fp);
    }

    // }}}

    // provider {{{

    public static function basicProvider()
    {
        $obj = new sample();
        $obj->MyInt = 99;
        $obj->MyFloat = 123.45;
        $obj->MyBool = true;
        $obj->MyNull = null;
        $obj->MyString = "Hello World";

        return array(

                // integers
                array(          0, '0'),
                array(        123, '123'),
                array(       -123, '-123'),
                array( 2147483647, '2147483647'),
                array(-2147483648, '-2147483648'),

                // floats 
                array(123.456,  '123.456'),
                array(  1.23E3, '1230'),
                array( -1.23E3, '-1230'),

                 // boolean
                array(TRUE,  'true'),
                array(true,  'true'),
                array(FALSE, 'false'),
                array(false, 'false'),

                // NULL
                array(NULL, 'null'),
                array(null, 'null'),

                // strings
                array("abc",              '"abc"'),
                array('abc',              '"abc"'),
                array("Hello\t\tWorld\n", '"Hello\t\tWorld\n"'),

                // arrays
                array(array(),                                                                                  '[]'),
                array(array(1,2,3,4,5),                                                                         '[1,2,3,4,5]'),
                array(array(1 => "Sun", 2=>"Mon", 3 => "Tue", 4 => "Wed", 5 => "Thur", 6 => "Fri", 7 => "Sat"), '{"1":"Sun","2":"Mon","3":"Tue","4":"Wed","5":"Thur","6":"Fri","7":"Sat"}'),
                array(array("Jan" => 31, "Feb" => 29, "Mar" => 31, "April" => 30, "May" => 31, "June" => 30),   '{"Jan":31,"Feb":29,"Mar":31,"April":30,"May":31,"June":30}'),

                // empty data
                array("", '""'),
                array('', '""'),

                // object variable
                array($obj , '{"MyInt":99,"MyFloat":123.45,"MyBool":true,"MyNull":null,"MyString":"Hello World"}'),

            );
    }

    public static function realEncodingProvider()
    {
        return array(

                array(null, 'null'),
                array(true, 'true'),
                array(false, 'false'),
                array(new \stdClass, '{}'),
                array(array(), '[]'),
                array('', '""'),

            );
    }

    // }}}
}

// vim:set foldmethod=marker:
