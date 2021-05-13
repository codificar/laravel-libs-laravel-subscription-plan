@extends('layout.master')

@section('breadcrumbs')
<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor m-b-0 m-t-0">{{trans('subscriptionPlanTrans::signature.signature')}}</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active">{{trans('subscriptionPlanTrans::signature.plural')}}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div id="VueJs">
	<signature-list
        signatures = "{{ $signatures }}"
        suspendOrActivate="{{ AuthUtils::hasPermissionByUrl('AdminUserEdit') }}"
        edit-permission="{{ AuthUtils::hasPermissionByUrl('AdminUserEdit') }}"
        delete-permission="{{ AuthUtils::hasPermissionByUrl('AdminDeleteUser') }}">
    </signature-list>
</div>
@endsection

@section('javascripts')
<script src="/libs/signature/lang.trans/plan,signature"> </script> 
<script src="{{ elixir('vendor/codificar/laravel-subscription-plan/subscriptionPlan.vue.js') }}"> </script> 
@endsection