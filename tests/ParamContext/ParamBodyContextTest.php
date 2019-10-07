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

namespace Paramee\ParamContext;

use Paramee\Model\ParameterValuesContext;
use Paramee\Model\ParameterValuesContextTest;
use Psr\Log\LoggerInterface;

/**
 * Class ParamBodyContextTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ParamBodyContextTest extends ParameterValuesContextTest
{
    public function testGetDeserializer()
    {
        $this->assertNull($this->getContextInstance()->getDeserializer());
    }

    protected function getContextInstance(LoggerInterface $logger = null): ParameterValuesContext
    {
        return new ParamBodyContext(null, $logger);
    }

    protected function getExpectedName(): string
    {
        return 'body';
    }
}