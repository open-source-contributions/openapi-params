<?php
/**
 * Paramee Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/paramee
 * @package caseyamcl/paramee
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE.md
 *  file that was distributed with this source code.
 *
 *  ------------------------------------------------------------------
 */

namespace Paramee\Format;

use Paramee\AbstractParamFormatTest;
use Paramee\Contract\ParamFormatInterface;
use Paramee\Model\Parameter;
use Paramee\Model\ParameterValidationRule;
use Paramee\Type\StringParameter;
use Respect\Validation\Validator;

/**
 * Class CsvFormatTest
 * @package Paramee\Format
 */
class CsvFormatTest extends AbstractParamFormatTest
{
    public function testValidateEach()
    {
        /** @var CsvFormat $format */
        $format = $this->getFormat();
        $validator = new ParameterValidationRule(Validator::equals('a'), 'test');
        $format->setValidatorForEach($validator);
        $param = (new StringParameter('test'))->setFormat($format);
        $this->assertEquals(['a', 'a', 'a'], $param->prepare('a, a, a'));
    }

    public function testSetSeparator()
    {
        /** @var CsvFormat $format */
        $format = $this->getFormat();
        $format->setSeparators('|;');
        $param = (new StringParameter('test'))->setFormat($format);
        $this->assertSame(['test', 'test1', 'test2'], $param->prepare('test;test1|test2'));
    }

    /**
     * @return ParamFormatInterface
     */
    protected function getFormat(): ParamFormatInterface
    {
        return new CsvFormat();
    }

    /**
     * @return Parameter
     */
    protected function getParameterWithFormat(): Parameter
    {
        return (new StringParameter('test'))->setFormat(new CsvFormat());
    }
}