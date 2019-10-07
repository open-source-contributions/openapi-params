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

use PHPUnit\Framework\TestCase;
use Paramee\Contract\ParameterValidationRuleInterface;
use Paramee\Contract\ParamFormatInterface;
use Paramee\Contract\PreparationStepInterface;

abstract class AbstractParamFormatTest extends TestCase
{
    public function testToString()
    {
        $constantName = sprintf("%s::NAME", get_class($this->getFormat()));
        if (defined($constantName)) {
            $this->assertEquals(constant($constantName), (string) $this->getFormat());
        }
    }

    public function testGetPreparationSteps()
    {
        $this->assertIsArray($this->getFormat()->getPreparationSteps());
        $this->assertContainsOnlyInstancesOf(
            PreparationStepInterface::class,
            $this->getFormat()->getPreparationSteps()
        );
    }

    public function testAppliesToType()
    {
        $this->assertNotEmpty($this->getFormat()->appliesToType());
    }

    public function testGetValidationRules()
    {
        $this->assertIsArray($this->getFormat()->getValidationRules());
        $this->assertContainsOnlyInstancesOf(
            ParameterValidationRuleInterface::class,
            $this->getFormat()->getValidationRules()
        );
    }

    /**
     * Test that a parameter with the given format contains the expected documentation
     */
    public function testExpectedDocumentationExists()
    {
        $param = $this->getParameterWithFormat();
        $expected = $param->getFormat()->getDocumentation();

        $this->assertStringContainsString((string) $expected, $param->getDescription());
    }

    // --------------------------------------------------------------

    /**
     * @return ParamFormatInterface
     */
    abstract protected function getFormat(): ParamFormatInterface;

    /**
     * @return Parameter
     */
    abstract protected function getParameterWithFormat(): Parameter;
}
