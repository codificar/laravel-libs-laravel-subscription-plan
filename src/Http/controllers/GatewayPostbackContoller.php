<?php

namespace Codificar\LaravelSubscriptionPlan\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Transaction, Invoice;
use Codificar\LaravelSubscriptionPlan\JobsSubscriptionBilletPaid;
use PaymentFactory;

class GatewayPostbackController extends Controller
{
    /**
     * Recebe uma notificação quando o status da transação muda
     */
    public function postback(Request $request)
    {
        $gateway = PaymentFactory::createGateway();
        $postbackTransaction = $gateway->billetVerify($request);
        
        $transaction = Transaction::getTransactionByGatewayId($postbackTransaction['transaction_id']);

        if ($transaction && $postbackTransaction['success'] && $postbackTransaction['status'] == 'paid') {
            $transaction->status = $postbackTransaction['status'];
            $transaction->save();
            
            $signature = $transaction->Signature;

            if ($signature)
                SubscriptionBilletPaid::dispatch($signature);
            
        }

        $invoice = Invoice::getByGatewayTransactionId($postbackTransaction['transaction_id']);

        if ($invoice && $postbackTransaction['success'] && $postbackTransaction['status'] == 'paid') {
            $invoice->is_paid = true;
            $invoice->status = Invoice::$PAID;
            
			$invoice->save();
        }
    }
}
