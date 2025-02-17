@extends('layout.provider.master')

@section('breadcrumbs')
	<div class="row page-titles">
		<div class="col-md-6 col-8 align-self-center">
			<h3 class="text-themecolor m-b-0 m-t-0">{{ trans('subscriptionPlanTrans::user_provider_web.plans') }}</h3>
			<ol class="breadcrumb">
				<li class="breadcrumb-item">
					<a href="javascript:void(0)">
						{{ trans("subscriptionPlanTrans::dashboard.home") }}
					</a>
				</li>
				<li class="breadcrumb-item active">{{ trans('subscriptionPlanTrans::user_provider_web.plans') }}</li>
			</ol>
		</div>
	</div>
@stop

@section('content')
	<div id="VueJs">
		<plans-grid 
			:planslist="{{ $plans }}"
			:validsignature="{{ $validSignature }}"
			:url-pix="'{{ route('providerPixScreen') }}'">
		</plans-grid>	
	</div>
@endsection

@section('javascripts')
<script src="/libs/signature/lang.trans/user_provider_web"> </script> 
<script src="{{ asset('vendor/codificar/subscription-plan/js/subscriptionPlan.vue.js') }}"> </script> 
@endsection