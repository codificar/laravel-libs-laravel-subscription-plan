<?php

namespace Codificar\LaravelSubscriptionPlan\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Http\Request;
use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Illuminate\Pagination\Paginator;

use Transaction as TransactionProject;

class Transaction extends TransactionProject
{
	//transaction status
	const PROCESSING 		= 'processing';
	const AUTHORIZED 		= 'authorized';
 	const PAID 				= 'paid';
 	const WAITING_PAYMENT 	= 'waiting_payment';
 	const PENDING_REFUND 	= 'pending_refund';
 	const REFUNDED 			= 'refunded';
 	const REFUSED 			= 'refused';
 	const ERROR 			= 'error';
 	const FAILED 			= 'failed';

	const MapStatus 		= array(
		'processing'		=> self::PROCESSING ,
		'authorized'		=> self::AUTHORIZED ,
		'paid'				=> self::PAID ,
		'waiting_payment'	=> self::WAITING_PAYMENT ,
		'pending_refund'	=> self::PENDING_REFUND ,
		'refunded'			=> self::REFUNDED ,
		'refused'			=> self::REFUSED ,
		'error'				=> self::ERROR ,
		'succeeded'			=> self::PAID ,
		'pending'			=> self::WAITING_PAYMENT ,
		'failed'			=> self::ERROR ,
	);

	/**
	 * Save a transaction
	 * 
	 * @param string $status
	 * @param float $planPrice
	 * @param float $paymentTax
	 * @param float $paymentFee
	 * @param string $transactionId
	 * @return object
	 */
	public static function saveSignatureTransaction(
		$status, 
		$planPrice, 
		$paymentTax, 
		$paymentFee, 
		$transactionId, 
		$billetLink = null, 
		$ledgerId = null,
		$pixBase64 = null,
		$pixCopyPaste = null,
		$pixExpirationDateTime = null,
	) {

		try {
			$transaction 							= new Transaction;
			$transaction->type 						= TRANSACTION::SUBSCRIPTION_TRANSACTION;
			$transaction->status 					= $status;
			$transaction->gross_value 	 			= $planPrice;
			$transaction->provider_value 			= 0;
			$transaction->gateway_tax_value 		= ($planPrice * $paymentTax) + $paymentFee;
			$transaction->net_value 				= $planPrice - $transaction->gateway_tax_value;
			$transaction->gateway_transaction_id 	= $transactionId;
			$transaction->ledger_id 				= $ledgerId;
			$transaction->pix_base64 				= $pixBase64;
			$transaction->pix_copy_paste 			= $pixCopyPaste;
			$transaction->pix_expiration_date_time 	= $pixExpirationDateTime;
			$transaction->billet_link 				= $billetLink;
			$transaction->save();

			return $transaction;
		} catch (\Exception $e) {
			\Log::error($e->getMessage());
		}
	}

	/**
	 * Returns subscription transaction data
	 * 
	 * @param $signatureId id da assinatura
	 * @return Transaction|null  
	 */
	public static function getSignatureTransaction($signatureId)
	{
		return self::where(['signature_id' => $signatureId])->latest('id')->first();

	}

	/**
	 * Returns the status of the transaction
	 * 
	 * @return String status da transação
	 */
	public function getStatus()
	{
		return $this::MapStatus[$this->status];
	}

	/**
	 * Returns whether the transaction status has been paid
	 * 
	 * @return boolean 
	 */
	public function isPaid()
	{
		return $this->status == self::PAID;
	}

	/**
	 * Returns if transaction status failed
	 * 
	 * @return boolean 
	 */
	public function isFail()
	{
		return $this->status == self::ERROR || $this->status == self::FAILED;
	}

	/**
	 * Add Subscription ID
	 * 
	 * @param int $id id da assinatura
	 * @return void
	 */
	public function setSignatureId($id)
	{
		$this->signature_id = $id;
		$this->save();
	}
}