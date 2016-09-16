<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Payment;
use App\Order;
use App\User;
use Config;

class DonateController extends Controller
{
    public function payment(Request $request)
    {
        $payment = new Payment(
            Config::get('payment.merchantId'), Config::get('payment.secret1'), Config::get('payment.secret2')
        );
        $getarray = array(
            'oa' => $request->get('AMOUNT'),
            'o' => $request->get('MERCHANT_ORDER_ID'),
            's' => $request->get('SIGN')
        );
        if ($payment->validateResult($getarray)) {
            $order = Order::find($payment->getInvoiceId());

            if (($payment->getSum() == $order->amount) && ($order->status == 0)) {
                $order->status = 1;
                $order->save();
                $user = User::find($order->user_id);
                $user->money = $user->money + $order->amount;
                $user->save();
                return $payment->getSuccessAnswer(); // "OK1254487\n"
            }
            return 'Error payment';
        }
        return 'Error validate Result';
    }
    public function merchant(Request $request) {
        $payment = new Payment(
            Config::get('payment.merchantId'), Config::get('payment.secret1'), Config::get('payment.secret2')
        );
        $user = $this->user;
        if($request->get('sum') < 1) {
            return response()->json(['msg' =>'сумма не может быть меньше 0', 'status' => 'error']);
        }
        $order = Order::create([
            'user_id' => $user->id,
            'amount' => $request->get('sum'),
            'status' => 0
        ]);
        $payment
            ->setInvoiceId($order->id)
            ->setSum($order->amount);
        return response()->json(['url' => $payment->getPaymentUrl(), 'status' => 'success']);
    } 
    public function success() {
        return redirect(Config::get('payment.url'));
    }
    public function fail() {
        return redirect(Config::get('payment.url'));
    }
}
