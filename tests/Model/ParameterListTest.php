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

namespace Paramee\Model;

use ArrayObject;
use DateTime;
use Paramee\Exception\AggregateErrorsException;
use Paramee\Contract\ParameterException;
use Paramee\ParamContext\ParamQueryContext;
use Paramee\PreparationStep\CallbackStep;
use Paramee\Type\ArrayParameter;
use Paramee\Type\BooleanParameter;
use Paramee\Type\IntegerParameter;
use Paramee\Type\StringParameter;
use PHPUnit\Framework\TestCase;

class ParameterListTest extends TestCase
{
    public function testConstructor(): void
    {
        $obj = new ParameterList('test');
        $this->assertInstanceOf(ParameterList::class, $obj);
    }

    public function testAddCsvValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addCsvValue('test');
        $prepared = $obj->prepare(['test' => 'a,b,c']);
        $this->assertEquals(['a', 'b', 'c'], $prepared->getPreparedValue('test'));
    }

    public function testAddNumber(): void
    {
        $obj = new ParameterList('test');
        $obj->addNumber('test')->setAllowTypeCast(true);
        $prepared = $obj->prepare(['test' => '25.2']);
        $this->assertSame(25.2, $prepared->getPreparedValue('test'));
    }

    public function testAddUuidValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addUuidValue('test');
        $prepared = $obj->prepare(['test' => 'e0959969-28d9-4572-9bf6-f970e4e9696e']);
        $this->assertSame('e0959969-28d9-4572-9bf6-f970e4e9696e', $prepared->getPreparedValue('test'));
    }

    public function testAddInteger(): void
    {
        $obj = new ParameterList('test');
        $obj->addInteger('test');
        $prepared = $obj->prepare(['test' => 12]);
        $this->assertSame(12, $prepared->getPreparedValue('test'));
    }

    public function testAddYesNoValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addYesNoValue('test');
        $prepared = $obj->prepare(['test' => 'on']);
        $this->assertSame(true, $prepared->getPreparedValue('test'));
    }

    public function testAddDateValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addDateValue('test');
        $prepared = $obj->prepare(['test' => '2019-05-12']);
        $this->assertSame('2019-05-12', $prepared->getPreparedValue('test')->format('Y-m-d'));
    }

    public function testAddBinaryValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addBinaryValue('test');
        $prepared = $obj->prepare(['test' => '011011']);
        $this->assertSame('011011', $prepared->getPreparedValue('test'));
    }

    public function testAddDateTimeValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addDateTimeValue('test');
        $prepared = $obj->prepare(['test' => '2017-07-21T17:32:28Z']);
        $this->assertSame(
            '2017-07-21T17:32:28+00:00',
            $prepared->getPreparedValue('test')->format(DateTime::RFC3339)
        );
    }


    public function testAddBooleanValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addBooleanValue('test');
        $prepared = $obj->prepare(['test' => true]);
        $this->assertSame(true, $prepared->getPreparedValue('test'));
    }

    public function testAddByteValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addByteValue('test');
        $prepared = $obj->prepare(['test' => base64_encode('test')]);
        $this->assertSame('test', $prepared->getPreparedValue('test'));
    }

    public function testAddAlphaNumericValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addAlphaNumericValue('test');
        $prepared = $obj->prepare(['test' => 'abc123']);
        $this->assertSame('abc123', $prepared->getPreparedValue('test'));
    }

    public function testAddArrayValue(): void
    {
        $obj = new ParameterList('test');
        $param = $obj->addArrayValue('test');
        $param->setUniqueItems(true);
        $allValues = new ParameterValues(['test' => 'a=apple,b=banana'], new ParamQueryContext());

        $param->addPreparationStep(new CallbackStep(function (array $value) {
            return array_map('strtoupper', $value);
        }, 'convert to uppercase'));
        $this->assertSame(['A', 'B', 'C'], $param->prepare('a,b,c', $allValues));
    }

    public function testAddObjectValue(): void
    {
        $obj = new ParameterList('test', []);

        $allValues = new ParameterValues(['test' => 'a=apple,b=banana'], new ParamQueryContext());

        $param = $obj->addObjectValue('test');
        $this->assertEquals(
            (object) ['a' => 'apple', 'b' => 'banana'],
            $param->prepare('a=apple,b=banana', $allValues)
        );
    }

    public function testAddPasswordValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addPasswordValue('test');
        $this->assertSame('test', $obj->prepare(['test' => 'test'])->getPreparedValue('test'));
    }

    public function testAddStringValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addStringValue('test');
        $this->assertSame('test', $obj->prepare(['test' => 'test'])->getPreparedValue('test'));
    }

    public function testAddEmailValue(): void
    {
        $obj = new ParameterList('test');
        $obj->addEmailValue('test');
        $this->assertSame(
            'test@example.org',
            $obj->prepare(['test' => 'test@example.org'])->getPreparedValue('test')
        );
    }

    public function testGetReturnsParameterWhenItExists(): void
    {
        $obj = new ParameterList('test');
        $param = $obj->addStringValue('test');
        $this->assertInstanceOf(StringParameter::class, $param);
    }

    public function testGetThrowsRuntimeExceptionWhenParamDoesNotExist(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Parameter not found');
        $obj = new ParameterList('test');
        $obj->get('nonexistent');
    }

    public function testGetName(): void
    {
        $obj = new ParameterList('test');
        $this->assertSame('test', $obj->getName());
    }

    public function testGetParameters(): void
    {
        $params = [
            new StringParameter('test'),
            new ArrayParameter('test2')
        ];

        $obj = new ParameterList('test', $params);
        $this->assertInstanceOf(ArrayObject::class, $obj->getParameters());
        $this->assertSame(2, $obj->count());
    }

    public function testGetContext(): void
    {
        $obj = new ParameterList('test', [], new ParamQueryContext());
        $this->assertInstanceOf(ParamQueryContext::class, $obj->getContext());
    }

    public function testCount(): void
    {
        $params = [
            new StringParameter('test'),
            new ArrayParameter('test2')
        ];

        $obj = new ParameterList('test', $params);
        $this->assertSame(2, $obj->count());
    }

    public function testPrepareWithUndefinedValuesAndStrictIsTrue(): void
    {
        $this->expectException(AggregateErrorsException::class);
        $this->expectExceptionMessage('Undefined parameter: test3');

        $params = [
            (new StringParameter('test')),
            (new ArrayParameter('test2'))
        ];

        $obj = new ParameterList('test', $params);
        $obj->prepare(['test' => 'a', 'test2' => ['a', 'b'], 'test3' => 't']);
    }

    public function testPrepareWithUndefinedValuesAndStrictIsFalse(): void
    {
        $params = [
            (new StringParameter('test')),
            (new ArrayParameter('test2'))
        ];

        $obj = new ParameterList('test', $params);
        $prepared = $obj->prepare(['test' => 'a', 'test2' => ['a', 'b'], 'test3' => 't'], false);
        $this->assertEquals(['test', 'test2', 'test3'], $prepared->listNames());
    }

    public function testPrepareWithMissingRequiredValues(): void
    {
        $this->expectException(AggregateErrorsException::class);
        $this->expectExceptionMessage('Missing required parameter: test2');

        $params = [
            (new StringParameter('test'))->makeRequired(),
            (new ArrayParameter('test2'))->makeRequired()
        ];

        $obj = new ParameterList('test', $params);
        $obj->prepare(['test' => 'a'], false);
    }

    public function testPrepareWithInvalidValues(): void
    {
        $params = [
            (new IntegerParameter('test'))->makeRequired(),
            (new BooleanParameter('test2'))->makeRequired()
        ];

        try {
            $obj = new ParameterList('test', $params);
            $obj->prepare(['test' => 'a', 'test2' => 'b']);
        } catch (AggregateErrorsException $e) {
            $this->assertStringContainsString('There were 2 validation errors', $e->getMessage());
            $this->assertEquals(2, $e->count());

            foreach ($e as $ex) {
                $this->assertInstanceOf(ParameterException::class, $ex);
            }

            return;
        }

        $this->fail('Should not have made it here');
    }

    public function testGetApiDocumentationReturnsEmptyArrayWhenNoParametersAreAdded(): void
    {
        $obj = new ParameterList('test');
        $this->assertSame([], $obj->getApiDocumentation());
    }

    public function testGetApiDocumentationReturnsExpectedValuesWhenParametersAreAdded(): void
    {
        $obj = new ParameterList('test');
        $obj->addStringValue('test1')
            ->makeRequired()
            ->setMinLength(5)
            ->setMaxLength(10)
            ->setDescription('here');

        $obj->addArrayValue('test2')
            ->makeOptional()
            ->addAllowedParamDefinition(StringParameter::create()->makeOptional()->setDeprecated(true))
            ->addAllowedParamDefinition(IntegerParameter::create()->max(5));

        $this->assertSame([
            'test1' => [
                'type' => 'string',
                'required' => true,
                'description' => 'here',
                'minLength' => 5,
                'maxLength' => 10
            ],
            'test2' => [
                'type' => 'array',
                'items' => ['oneOf' => [
                    [
                        'type' => 'string',
                        'deprecated' => true
                    ],
                    [
                        'type' => 'integer',
                        'format' => 'int64',
                        'maximum' => 5.0
                    ]
                ]]
            ]
        ], $obj->getApiDocumentation());
    }

    public function testGetIterator(): void
    {
        $params = [new IntegerParameter('test'), new BooleanParameter('test2')];
        $obj = new ParameterList('test', $params);
        foreach ($obj as $item) {
            $this->assertInstanceOf(Parameter::class, $item);
        }
    }
}
