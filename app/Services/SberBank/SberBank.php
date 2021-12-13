<?php

namespace App\Services\SberBank;

use App\Helpers\ResultGenerate;
use App\Models\Payments;

class SberBank
{
    private $userName;
    private $password;
    const URL = 'https://3dsec.sberbank.ru/payment/rest/';

    public function __construct($test = true, $userName = 'T507803864501-api', $password = 'T507803864501')
    {
        $this->userName = $userName;
        $this->password = $password;
    }

    public function Register(Payments $payment, int $amount, string $returnPaidUrl, string $returnErrorUrl)
    {
        $orderNumber = $payment->order_id;

        $amount = $amount * 100;
        $data = [
            'userName' => $this->userName,
            'password' => $this->password,
            'orderNumber' => $orderNumber,
            'amount' => $amount,
            'returnUrl' => $returnPaidUrl,
            'failUrl' => $returnErrorUrl,
        ];

        $request = file_get_contents(self::URL . 'register.do' . '?' . http_build_query($data));

        $payment->bankResponse = $request;

        $request = json_decode($request, true);

        if (empty($request['errorCode'])) {                 // {"orderId":"a7cba16f-0e3a-76e6-8af5-e4932826ce6a","formUrl":"https:\/\/3dsec.sberbank.ru\/payment\/merchants\/sbersafe_sberid\/payment_ru.html?mdOrder=a7cba16f-0e3a-76e6-8af5-e4932826ce6a"}
            $payment->bankOrderId = $request['orderId'];
            $payment->link = $request['formUrl'];
        }

        $payment->save();

        if (empty($request['errorCode'])) {
            return (object)[
                'status' => true,
                'orderBank' => $request['orderId'],
                'paymentLink' => $request['formUrl'],
            ];
        } else {        // {"errorCode":"1","errorMessage":"Заказ с таким номером уже обработан"}
            return (object)[
                'status' => false,
                'errorCode' => $request['errorCode'],
                'errorMessage' => $request['errorMessage'],
            ];
        }
    }

    public function Refund(Payments $payment, $amount)
    {
        $orderNumber = $payment->bankOrderId;

        $amount = $amount * 100;
        $data = [
            'userName' => $this->userName,
            'password' => $this->password,
            'orderId' => $orderNumber,
            'amount' => $amount,
        ];

        $request = file_get_contents(self::URL . 'refund.do' . '?' . http_build_query($data));

        $request = json_decode($request, true);

        if ($request['errorCode'] === "0") {
            $payment->amount = $payment->amount - $amount / 100;
            $payment->save();
            return (object)[
                'status' => true,
                'payment' => $payment,
                'newAmount' => $payment->amount,
                'request' => $request,
                'message' => $request['errorMessage'],
            ];
        }

        return (object)[
            'status' => false,
            'request' => $request,
            'message' => $request['errorMessage'],
        ];
    }

    public function Deposit(Payments $payment, $amount)
    {
        $orderNumber = $payment->bankOrderId;

        $amount = $amount * 100;
        $data = [
            'userName' => $this->userName,
            'password' => $this->password,
            'orderId' => $orderNumber,
            'amount' => $amount,
        ];

        $request = file_get_contents(self::URL . 'deposit.do' . '?' . http_build_query($data));

        $request = json_decode($request, true);

        if ($request['errorCode'] === "0") {
            $payment->amount = $payment->amount - $amount / 100;
            $payment->save();
            return (object)[
                'status' => true,
                'payment' => $payment,
                'newAmount' => $payment->amount,
                'request' => $request,
                'message' => $request['errorMessage'],
            ];
        }

        return (object)[
            'status' => false,
            'request' => $request,
            'message' => $request['errorMessage'],
        ];
    }

    public function PaymentStatus(Payments $payment)
    {
        $orderNumber = $payment->bankOrderId;

        $data = [
            'userName' => $this->userName,
            'password' => $this->password,
            'orderId' => $orderNumber,
        ];

        $request = file_get_contents(self::URL . 'getOrderStatusExtended.do' . '?' . http_build_query($data));

        $request = json_decode($request, true);

        return $request;
    }
}