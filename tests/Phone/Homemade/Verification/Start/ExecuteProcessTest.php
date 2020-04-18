<?php

namespace Yosmy\Test\Phone\Homemade\Verification\Start;

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
        $template = 'template %s';

        $resolveCode = $this->createMock(Yosmy\Phone\Verification\ResolveCode::class);

        $code = $this->createMock(Yosmy\Phone\Verification\Code::class);

        $codeValue = 'code-value';

        $code->expects($this->once())
            ->method('getValue')
            ->with()
            ->willReturn($codeValue);

        $resolveCode->expects($this->once())
            ->method('resolve')
            ->with(
                $country,
                $prefix,
                $number
            )
            ->willReturn($code);

        $text = sprintf($template, $codeValue);

        $sendSms = $this->createMock(Yosmy\Phone\SendSms::class);

        $sendSms->expects($this->once())
            ->method('send')
            ->with(
                $country,
                $prefix,
                $number,
                $text
            );

        $executeProcess = new Yosmy\Phone\Homemade\Verification\Start\ExecuteProcess(
            $resolveCode,
            $sendSms
        );

        try {
            $executeProcess->execute(
                $country,
                $prefix,
                $number,
                $template
            );
        } catch (Yosmy\Phone\VerificationException $e) {
            throw new LogicException();
        }
    }

    /**
     * @throws Yosmy\Phone\VerificationException
     */
    public function testExecuteHavingSmsException()
    {
        $country = 'country';
        $prefix = 'prefix';
        $number = 'number';
        $template = 'template %s';

        $resolveCode = $this->createMock(Yosmy\Phone\Verification\ResolveCode::class);

        $code = $this->createMock(Yosmy\Phone\Verification\Code::class);

        $codeValue = 'code-value';

        $code->expects($this->once())
            ->method('getValue')
            ->with()
            ->willReturn($codeValue);

        $resolveCode->expects($this->once())
            ->method('resolve')
            ->willReturn($code);

        $sendSms = $this->createMock(Yosmy\Phone\SendSms::class);

        $sendSms->expects($this->once())
            ->method('send')
            ->willThrowException(new Yosmy\Phone\SmsException());

        $executeProcess = new Yosmy\Phone\Homemade\Verification\Start\ExecuteProcess(
            $resolveCode,
            $sendSms
        );

        $this->expectExceptionObject(new Yosmy\Phone\VerificationException('Ocurrió un error con el envío del sms. Por favor intenta más tarde'));

        $executeProcess->execute(
            $country,
            $prefix,
            $number,
            $template
        );
    }
}