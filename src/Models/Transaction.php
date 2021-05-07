<?php

namespace Codificar\LaravelSubscriptionPlan\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Http\Request;
use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Illuminate\Pagination\Paginator;

use Transaction as TransactionProject;

class Transaction extends TransactionProject
{
	/**
	 * Salva uma transação referente a assinatura
	 * @param string $status
	 * @param float $planPrice
	 * @param float $paymentTax
	 * @param float $paymentFee
	 * @param string $transactionId
	 * @return object
	 */
	public static function saveSignatureTransaction($status, $planPrice, $paymentTax, $paymentFee, $transactionId, $billetLink = null) {

		try {
			$transaction 					= new Transaction;
			$transaction->type 				= TRANSACTION::SUBSCRIPTION_TRANSACTION;
			$transaction->status 			= $status;
			$transaction->gross_value 	 	= $planPrice;
			$transaction->provider_value 	= 0;
			$transaction->gateway_tax_value 	= ($planPrice * $paymentTax) + $paymentFee;
			$transaction->net_value 			= $planPrice - $transaction->gateway_tax_value;
			$transaction->gateway_transaction_id = $transactionId;
			$transaction->billet_link = $billetLink;
			$transaction->save();

			return $transaction;
		} catch (\Exception $e) {
			\Log::error($e->getMessage());
		}
	}

	/**
	 * Adiciona o id da assinatura
	 * @param int $id id da assinatura
	 * @return void
	 */
	public function setSignatureId($id)
	{
		$this->signature_id = $id;
		$this->save();
	}
}