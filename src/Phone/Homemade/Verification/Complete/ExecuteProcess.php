<?php

namespace Yosmy\Phone\Homemade\Verification\Complete;

use Yosmy\Phone\Verification;
use Yosmy\Phone\VerificationException;

/**
 * @di\service()
 */
class ExecuteProcess implements Verification\Complete\ExecuteProcess
{
    /**
     * @var Verification\AssertCode
     */
    private $assertCode;

    /**
     * @param Verification\AssertCode $assertCode
     */
    public function __construct(Verification\AssertCode $assertCode)
    {
        $this->assertCode = $assertCode;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(
        string $country,
        string $prefix,
        string $number,
        string $code
    ) {
        try {
            $assert = $this->assertCode->assert(
                $country,
                $prefix,
                $number,
                $code
            );
        } catch (Verification\Code\ExpiredValueException $e) {
            throw new VerificationException('El código ha expirado');
        }

        if (!$assert) {
            throw new VerificationException('El código entrado es incorrecto');
        }
    }
}