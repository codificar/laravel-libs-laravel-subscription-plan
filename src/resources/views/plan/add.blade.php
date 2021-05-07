@extends('layout.master')
@section('breadcrumbs')
<div class="row page-titles">
	<div class="col-md-6 col-8 align-self-center">
		<h3 class="text-themecolor m-b-0 m-t-0">{{trans('subscriptionPlanTrans::plan.plan')}}</h3>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			@if(isset($plan->id))
				<li class="breadcrumb-item active">{{trans('subscriptionPlanTrans::plan.plan_edit')}}</li>
			@else
				<li class="breadcrumb-item active">{{trans('subscriptionPlanTrans::plan.plan_add')}}</li>
			@endif
			
		</ol>
	</div>
</div>
@stop
@section('content')
<div id="VueJs">
	<plan-edit
		redirect="/admin/plan/list"
		edit="{{ $edit }}"
		Plan="{{ $plan }}"
		locations="{{ $locations }}">
	</plan-edit>
</div>
@endsection
@section('javascripts')
<script src="/libs/signature/lang.trans/plan,payment"> </script> 
<script src="{{ elixir('vendor/codificar/laravel-signature/signature.vue.js') }}"> </script> 
@endsection