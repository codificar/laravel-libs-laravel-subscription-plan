@extends('layout.master')

@section('breadcrumbs')
<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor m-b-0 m-t-0">{{trans('subscriptionPlanTrans::plan.plan')}}</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active">{{trans('subscriptionPlanTrans::plan.plural')}}</li>
        </ol>
    </div>
    <div class="col-md-6 col-4 align-self-center">
		<button onclick="location.href='{{ URL::Route('AddPlan') }}'" class="btn pull-right hidden-sm-down btn-success"><i class="mdi mdi-plus-circle"></i> {{trans('subscriptionPlanTrans::plan.add_new_plan')}}</button>
	</div>
</div>
@stop

@section('content')
<div id="VueJs">
    <plan-list
            plans = "{{ $plans }}"
            edit-permission="{{ AuthUtils::hasPermissionByUrl('AdminUserEdit') }}"
            delete-permission="{{ AuthUtils::hasPermissionByUrl('AdminDeleteUser') }}">
    </plan-list>
</div>
@endsection

@section('javascripts')
<script src="/libs/signature/lang.trans/plan,signature"> </script> 
<script src="{{ asset('vendor/codificar/subscription-plan/js/subscriptionPlan.vue.js') }}"> </script> 
@endsection