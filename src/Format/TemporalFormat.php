<?php
/**
 *  Paramee Library
 *
 *  @license http://opensource.org/licenses/MIT
 *  @link https://github.com/caseyamcl/paramee
 *  @package caseyamcl/paramee
 *  @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE.md
 *  file that was distributed with this source code.
 *
 *  ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Paramee\Format;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use InvalidArgumentException;
use Respect\Validation\Validator;
use Paramee\Contract\ParameterValidationRuleInterface;
use Paramee\Model\ParameterValidationRule;
use Paramee\PreparationStep\CallbackStep;
use Paramee\Type\StringParameter;

/**
 * Temporal format (any parse-able date string)
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class TemporalFormat extends DateTimeFormat
{
    public const NAME = 'temporal';
    public const TYPE_CLASS = StringParameter::class;

    public function getPreparationSteps(): array
    {
        return [
            new CallbackStep(function (string $value): DateTimeImmutable {
                return CarbonImmutable::parse($value);
            }, 'parse date string to Chronos instance')
        ];
    }

    /**
     * @return string|null
     */
    public function getDocumentation(): ?string
    {
        return 'Value must be a valid date/time (examples: "now", "2017-03-04", "tomorrow", "2017-03-04T01:30:40Z").';
    }

    /**
     * @return ParameterValidationRuleInterface
     */
    protected function getBaseRule(): ParameterValidationRuleInterface
    {
        return new ParameterValidationRule(
            Validator::callback(function (string $value) {
                try {
                    return (bool) CarbonImmutable::parse($value);
                } catch (InvalidArgumentException $e) {
                    return false;
                }
            })
        );
    }


}
