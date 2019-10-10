<?php

/**
 *  Paramee Library
 *
 *  @license http://opensource.org/licenses/MIT
 *  @link https://github.com/caseyamcl/paramee
 *  @author Casey McLaughlin <caseyamcl@gmail.com> caseyamcl/openapi-params
 *  @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE.md
 *  file that was distributed with this source code.
 *
 *  ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace OpenApiParams\Format;

use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use Respect\Validation\Validator;
use OpenApiParams\Contract\ParamValidationRule;
use OpenApiParams\Model\ParameterValidationRule;
use OpenApiParams\PreparationStep\CallbackStep;
use OpenApiParams\Type\StringParameter;

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
            }, 'parse date string to CarbonImmutable instance')
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
     * @return ParamValidationRule
     */
    protected function getBaseRule(): ParamValidationRule
    {
        return new ParameterValidationRule(
            Validator::callback(function (string $value) {
                try {
                    return (bool) CarbonImmutable::parse($value);
                } catch (Exception | InvalidArgumentException $e) {
                    return false;
                }
            }),
            sprintf('value must be valid RFC3339 date/time (example: %s)', static::DATE_FORMAT_EXAMPLE)
        );
    }
}
