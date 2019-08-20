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

namespace Paramee\Exception;

use RuntimeException;
use Paramee\Contract\PreparationStepInterface;
use Paramee\Model\ParameterError;
use Webmozart\Assert\Assert;

/**
 * This is thrown when parameter data is invalid.  This must be thrown from a PreparationStep
 *
 * It usually translates to 422, but can be 400 or whatever.
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
final class InvalidParameterException extends RuntimeException
{
    /**
     * @var PreparationStepInterface
     */
    private $step;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var array|ParameterError[]
     */
    private $errors;

    /**
     * From single message
     *
     * @param PreparationStepInterface $step
     * @param string $paramName  Full parameter name
     * @param $value
     * @param string $message
     * @return InvalidParameterException
     */
    public static function fromMessage(PreparationStepInterface $step, string $paramName, $value, string $message)
    {
        return static::fromMessages($step, $paramName, $value, [$message]);
    }

    /**
     * @param PreparationStepInterface $step
     * @param string $paramName
     * @param mixed $value
     * @param array|string[] $messages
     * @return InvalidParameterException
     */
    public static function fromMessages(PreparationStepInterface $step, string $paramName, $value, array $messages)
    {
        Assert::allString($messages);

        $errors = array_map(function (string $message) use ($paramName) {
            return new ParameterError($message, $paramName);
        }, $messages);

        return new static($step, $value, $errors);
    }

    /**
     * PreparationStepException constructor.
     *
     * @param PreparationStepInterface $step
     * @param mixed $value
     * @param array|ParameterError[] $errors
     */
    public function __construct(PreparationStepInterface $step, $value, array $errors)
    {
        Assert::allIsInstanceOf($errors, ParameterError::class);

        $message = sprintf('Parameter preparation step failed (invalid data): ' . get_class($step));
        $message .= '; ' . implode(PHP_EOL, $errors);

        parent::__construct($message, 422);

        $this->step = $step;
        $this->value = $value;
        $this->errors = $errors;
    }

    /**
     * Which step failed?
     *
     * @return PreparationStepInterface
     */
    public function getStep(): PreparationStepInterface
    {
        return $this->step;
    }

    /**
     * Get the raw parameter value that was invalid
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get messages for the end-user
     *
     * @return array|ParameterError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
