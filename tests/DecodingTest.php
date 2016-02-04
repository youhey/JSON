<?php
/**
 * PHP JSON wrapper
 *
 * @package Json
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Json;

/**
 * @package Json
 *
 * @author IKEDA Youhei <youhey.ikeda@gmail.com>
 */
class DecodingTest extends \PHPUnit_Framework_TestCase
{
    // \Json\Json::parse() {{{

    /**
     * @test
     * @dataProvider basicProvider
     */
    public function parse($json, $expected)
    {
        $result = Json::parse($json);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @dataProvider realDecodingProvider
     */
    public function parseRealDecoding($json, $expected)
    {
        $result = Json::parse($json);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function parseRealDecoding_Object()
    {
        $this->assertEquals(new \stdClass, Json::parse('{}', false));
        $this->assertEquals(array(), Json::parse('{}', true));
    }

    /**
     * @test
     */
    public function parseObject()
    {
        $json = '{"MyInt":99,"MyFloat":123.45,"MyBool":true,"MyNull":null,"MyString":"Hello World"}';

        $result = Json::parse($json, false);

        $expected = new \stdClass;
        $expected->MyInt    = 99;
        $expected->MyFloat  = 123.45;
        $expected->MyBool   = true;
        $expected->MyNull   = null;
        $expected->MyString = 'Hello World';

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function parseObject_toHash()
    {
        $json = '{"MyInt":99,"MyFloat":123.45,"MyBool":true,"MyNull":null,"MyString":"Hello World"}';

        $result = Json::parse($json, true);

        $expected = array();
        $expected['MyInt']    = 99;
        $expected['MyFloat']  = 123.45;
        $expected['MyBool']   = true;
        $expected['MyNull']   = null;
        $expected['MyString'] = 'Hello World';

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function parseBigInt_OldPHP()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $this->markTestSkipped('PHP Version >= 5.4.0');
        }

        $json = '{"number": 12345678901234567890}';

        $result = Json::parse($json);
        $this->assertInternalType('float', $result->number);
        $this->assertEquals('1.234568e+19', sprintf('%e', $result->number));
    }

    /**
     * @test
     */
    public function parseBigInt()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('PHP Version < 5.4.0');
        }

        $json = '{"number": 12345678901234567890}';

        $result = Json::parse($json);

        $expected = new \stdClass;
        $expected->number = '12345678901234567890';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function parseEmptyString()
    {
        $result = Json::parse('');
        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function parseWithBOM()
    {
        $json = file_get_contents(__DIR__.'/text/withbom.json');

        $result   = Json::parse($json);
        $expected = '1.  三剣豪-天下制覇';
        $this->assertEquals($expected, $result[0]->title);
    }

    /**
     * @test
     * @expectedException \Json\Exception\SyntaxError
     * @expectedExceptionMessage JSON decode failed: Syntax error (4)
     */
    public function parseBadJson_SingleQuarto()
    {
        $bad_json = "{ 'bar': 'baz' }";

        $result = Json::parse($bad_json);
    }

    /**
     * @test
     * @expectedException \Json\Exception\SyntaxError
     * @expectedExceptionMessage JSON decode failed: Syntax error (4)
     */
    public function parseBadJson_NoQuarto()
    {
        $bad_json = '{ bar: "baz" }';

        $result = Json::parse($bad_json);
    }

    /**
     * @test
     * @expectedException \Json\Exception\SyntaxError
     * @expectedExceptionMessage JSON decode failed: Syntax error (4)
     */
    public function parseBadJson_LastComma()
    {
        $bad_json = '{ bar: "baz", }';

        $result = Json::parse($bad_json);
    }

    /**
     * @test
     * @expectedException \Json\Exception\Decoding
     * @expectedExceptionMessage JSON decode failed: Maximum stack depth exceeded (1)
     */
    public function parseDepthError()
    {
        if (version_compare(PHP_VERSION, '5.3.0', '<')) {
            $this->markTestSkipped('PHP Version < 5.3.0');
        }

        // Default max-depth=512 ( x > 512 -> JSON_ERROR_DEPTH )
        $json = '[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]';

        $result = Json::parse($json);
    }

    // }}}

    // provider {{{

    public static function basicProvider()
    {
        return array(

                // integers
                array('0',                     0),
                array('123',                 123),
                array('-123',               -123),
                array('2147483647',   2147483647),
                array('-2147483648', -2147483648),

                // floats 
                array('123.456', 123.456),
                array('1230',      1.23E3),
                array('-1230',    -1.23E3),

                 // boolean
                array('true',  true),
                array('false', false),

                // NULL
                array('null', null),

                // strings
                array('"abc"',              'abc'),
                array('"Hello\t\tWorld\n"', "Hello\t\tWorld\n"),

                // arrays
                array('[]',                                                                       array()),
                array('[1,2,3,4,5]',                                                              array(1,2,3,4,5)),
                array('{"1":"Sun","2":"Mon","3":"Tue","4":"Wed","5":"Thur","6":"Fri","7":"Sat"}', (object)array(1 => "Sun", 2=>"Mon", 3 => "Tue", 4 => "Wed", 5 => "Thur", 6 => "Fri", 7 => "Sat")),
                array('{"Jan":31,"Feb":29,"Mar":31,"April":30,"May":31,"June":30}',               (object)array("Jan" => 31, "Feb" => 29, "Mar" => 31, "April" => 30, "May" => 31, "June" => 30)),

                // empty data
                array('""', ''),
            );
    }

    public static function realDecodingProvider()
    {
        return array(

                array('', null),
                array('null', null),
                array('true', true),
                array('false', false),
                array('[]', array()),
                array('""', ''),

            );
    }

    // }}}
}

// vim:set foldmethod=marker:
