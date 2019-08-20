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

use Paramee\Model\AbstractParamFormat;
use Paramee\Model\ParameterValidationRule;
use Paramee\Type\IntegerParameter;

/**
 * OpenAPI Int64 Format
 *
 * This is automatically assigned based on the system's numeric precision capabilities
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class Int64Format extends AbstractParamFormat
{
    public const TYPE_CLASS = IntegerParameter::class;

    /**
     * Get built-in validation rules
     *
     * These are added to the validation preparation step automatically
     *
     * @return array|ParameterValidationRule[]
     */
    public function getValidationRules(): array
    {
        return []; // no extra validation rules
    }
}
