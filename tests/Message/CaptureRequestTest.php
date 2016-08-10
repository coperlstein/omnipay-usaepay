<?php

namespace Omnipay\USAePay\Message;

use Omnipay\Tests\TestCase;

class CaptureRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());

        $this->request->initialize(array(
            'amount' => '10.00',
            'transactionReference' => '108868974',
        ));
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('10.00', $data['amount']);
    }

    public function testMockSendSuccess()
    {
        $this->setMockHttpResponse('CaptureSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('TESTMD', $response->getTransactionReference());
        $this->assertSame('', $response->getMessage());
    }

    public function testSendSuccess()
    {
        $this->request->setSandbox(true);
        $this->request->setTestMode(true);
        $this->request->setSource('_7M6zPa7P9k19g82M1aR8aOPvgFVcIWv');
        $this->request->setPin('123456');
        $this->request->setInvoice(substr(md5(rand()), 0, 10));
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertStringMatchesFormat('%d', $response->getTransactionReference());
        $this->assertSame('Approved', $response->getMessage());
    }

    public function testMockSendFailure()
    {
        $this->setMockHttpResponse('CaptureFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('000000', $response->getTransactionReference());
        $this->assertSame('Unable to find original transaction.', $response->getMessage());
    }

    public function testSendFailure()
    {
        $this->request->setSandbox(true);
        $this->request->setTestMode(true);
        $this->request->setSource('_7M6zPa7P9k19g82M1aR8aOPvgFVcIWv');
        $this->request->setPin('123456');
        $this->request->setInvoice(substr(md5(rand()), 0, 10));
        $this->request->setTransactionReference('100000000');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('000000', $response->getTransactionReference());
        $this->assertSame('Unable to find original transaction.', $response->getMessage());
    }
}
