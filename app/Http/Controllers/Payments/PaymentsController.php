<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Payments;
use App\Services\SberBank\SberBank;

class PaymentsController extends Controller
{
    public static function CreatePayment(Orders $order, $orderSumFront, $paymentType = 'cash')
    {
        $newPayment = Payments::create([
            'order_id' => $order->id,
            'status' => Payments::STATUS_TEXT['newPayment'],
            'type' => Payments::TYPE_TEXT[$paymentType],
            'amount' => $orderSumFront,
        ]);
        $order->payment_id = $newPayment->id;
        $order->save();
        return $newPayment;
    }

    public function PaymentPaidRequest()
    {
        $orderBank = request()->orderId;
        return self::PaymentPaid($orderBank);
    }

    public function PaymentErrorRequest()
    {
        $orderBank = request()->orderId;
        return self::PaymentError($orderBank);
    }

    public function PaymentRefundRequest()
    {
        $paymentId = request()->paymentId;
        $amount = request()->amount;

        $payment = Payments::find($paymentId);

        $paymentService = new SberBank();
        return $paymentService->Refund($payment, $amount);
    }

    public function PaymentStatusRequest()
    {
        $paymentId = request()->paymentId;

        $payment = Payments::find($paymentId);

        $paymentService = new SberBank();
        return $paymentService->PaymentStatus($payment);
    }

    public static function PaymentPaid($orderBank)
    {
        $orderBank = request()->orderId;
        $payment = Payments::where('bankOrderId', $orderBank)->first();
        $payment->status = Payments::STATUS_TEXT['paid'];
        return $payment->save();
    }

    public static function PaymentError($orderBank)
    {
        $payment = Payments::where('bankOrderId', $orderBank)->first();
        $payment->status = Payments::STATUS_TEXT['error'];
        return $payment->save();
    }
}