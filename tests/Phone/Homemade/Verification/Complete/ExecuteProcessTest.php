<?php

namespace Yosmy\Test\Phone\Homemade\Verification\Complete;

use Yosmy;
use PHPUnit\Framework\TestCase;
use LogicException;

class ExecuteProcessTest extends TestCase
{
    public function testExecute()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $code = 'code';

        $assertCode = $this->createMock(Yosmy\Phone\Verification\AssertCode::class);

        $assert = true;

        $assertCode->expects($this->once())
            ->method('assert')
            ->with(
                $country,
                $prefix,
                $number,
                $code
            )
            ->willReturn($assert);

        $executeProcess = new Yosmy\Phone\Homemade\Verification\Complete\ExecuteProcess(
            $assertCode
        );

        try {
            $executeProcess->execute(
                $country,
                $prefix,
                $number,
                $code
            );
        } catch (Yosmy\Phone\VerificationException $e) {
            throw new LogicException();
        }
    }

    /**
     * @throws Yosmy\Phone\VerificationException
     */
    public function testExecuteHavingExpiredValueException()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $code = 'code';

        $assertCode = $this->createMock(Yosmy\Phone\Verification\AssertCode::class);

        $assertCode->expects($this->once())
            ->method('assert')
            ->with(
                $country,
                $prefix,
                $number,
                $code
            )
            ->willThrowException(new Yosmy\Phone\Verification\Code\ExpiredValueException($code));

        $executeProcess = new Yosmy\Phone\Homemade\Verification\Complete\ExecuteProcess(
            $assertCode
        );

        $this->expectExceptionObject(new Yosmy\Phone\VerificationException('El código ha expirado'));

        $executeProcess->execute(
            $country,
            $prefix,
            $number,
            $code
        );
    }

    /**
     * @throws Yosmy\Phone\VerificationException
     */
    public function testExecuteHavingNoAssert()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $code = 'code';

        $assertCode = $this->createMock(Yosmy\Phone\Verification\AssertCode::class);

        $assert = false;

        $assertCode->expects($this->once())
            ->method('assert')
            ->with(
                $country,
                $prefix,
                $number,
                $code
            )
            ->willReturn($assert);

        $executeProcess = new Yosmy\Phone\Homemade\Verification\Complete\ExecuteProcess(
            $assertCode
        );

        $this->expectExceptionObject(new Yosmy\Phone\VerificationException('El código entrado es incorrecto'));

        $executeProcess->execute(
            $country,
            $prefix,
            $number,
            $code
        );
    }
}