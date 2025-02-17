@extends('layout.provider.master')

@section('breadcrumbs')
	<div class="row page-titles">
		<div class="col-md-6 col-8 align-self-center">
			<h3 class="text-themecolor m-b-0 m-t-0">{{ trans('subscriptionPlanTrans::user_provider_web.checkout_plan') }}</h3>
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a href="{{route('ProviderListPlans')}}">
                        {{ trans("subscriptionPlanTrans::dashboard.home") }}
					</a>
				</li>
				<li class="breadcrumb-item active">{{ trans('subscriptionPlanTrans::user_provider_web.checkout_plan') }}</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
<div id="VueJs">
	<checkout-plan 
		:plan="{{ $plan }}" 
		:payment="{{ $payment }}" 
		:provider="{{ $provider }}"
		:url-redirect="'{{ route('ProviderListPlans') }}'"
		:url-pix="'{{ route('providerPixScreen') }}'">
	</checkout-plan>
</div>
@endsection

@section('javascripts')
<script src="/libs/signature/lang.trans/user_provider_web"> </script> 
<script src="/plugins/card/card.js"></script>
<script src="{{ asset('vendor/codificar/subscription-plan/js/subscriptionPlan.vue.js') }}"> </script> 
@stop