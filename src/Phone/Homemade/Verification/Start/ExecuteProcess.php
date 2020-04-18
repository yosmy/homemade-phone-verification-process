<?php

namespace Yosmy\Phone\Homemade\Verification\Start;

use Yosmy\Phone\SendSms;
use Yosmy\Phone\SmsException;
use Yosmy\Phone\Verification;
use Yosmy\Phone\VerificationException;

/**
 * @di\service()
 */
class ExecuteProcess implements Verification\Start\ExecuteProcess
{
    /**
     * @var Verification\ResolveCode
     */
    private $resolveCode;

    /**
     * @var SendSms
     */
    private $sendSms;

    /**
     * @param Verification\ResolveCode $resolveCode
     * @param SendSms                  $sendSms
     */
    public function __construct(
        Verification\ResolveCode $resolveCode,
        SendSms $sendSms
    ) {
        $this->resolveCode = $resolveCode;
        $this->sendSms = $sendSms;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(
        string $country,
        string $prefix,
        string $number,
        string $template
    ) {
        $code = $this->resolveCode->resolve(
            $country,
            $prefix,
            $number
        );

        $text = sprintf($template, $code->getValue());

        try {
            $this->sendSms->send(
                $country,
                $prefix,
                $number,
                $text
            );
        } catch (SmsException $e) {
            throw new VerificationException('Ocurrió un error con el envío del sms. Por favor intenta más tarde');
        }
    }
}