<?php

namespace Codificar\LaravelSubscriptionPlan\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Http\Request;
use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Illuminate\Pagination\Paginator;

use Settings as SettingsProject;

class Settings extends SettingsProject
{
    /**
	 * Returns the number of days set for the payment slip to expire
	 * 
	 * @return string
	 */
	public static function getBilletExpirationDays()
	{
		$settings = self::where('key', 'billet_expiration_days')->first();

		if ($settings)
            return $settings->value;            
        else
			return 5;
	}

	/**
	 * Returns ticket instructions
	 * 
	 * @return string
	 */
	public static function getBilletInstructions()
	{
		$settings = self::where('key', 'billet_instructions')->first();

		if ($settings)
            return $settings->value;            
        else
			return "";
	}

	/**
	 * Get days for generate subscription recurrency before next expiration
	 * 
	 * @return int
	 */
	public static function getDaysForSubscriptionRecurrency()
	{
		return 3;
	}

}