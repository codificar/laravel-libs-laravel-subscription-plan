<template>
    <div class="col-lg-12">
        <div class="card card-outline-info">
            
            <div class="card-header">
                <h4 class="text-white m-b-0">{{ this.trans('user_provider_web.plans') }}</h4>
            </div>

            <div class="card-block">
                <div v-if="isValid.is_valid" class="actual-plan">
                    <div class="card-plan">
                        <h2>{{ this.trans('user_provider_web.actual_plan') + planValidName }}</h2>
                        <p class="text-muted sub-title">{{ this.trans('user_provider_web.expiry_in') + isValid.next_expiration_formated }}</p>
                    
                        <div class="container-status">
                            <div>
                                <p class="title">{{ this.trans('user_provider_web.status_plan') }}</p>
                            </div>
                            <div v-if="!isValid.activity">
                                <span class="span span-error">{{ this.trans('user_provider_web.no_active') }}</span>
                            </div>
                            <div v-else>
                                <span 
                                    v-if="isValid.activity" 
                                    class="span span-success">
                                    {{ this.trans('user_provider_web.active') }}
                                </span>
                            </div>
                        </div>
                        <div class="container">
                            <div>
                                <p class="title status">{{ this.trans('user_provider_web.status_payment') }}</p>
                            </div>
                            <div v-if="!isValid.isPaid">
                                <span v-if="isValid.status == 'error' " class="span span-error">{{ this.trans('user_provider_web.status_error') }}</span>
                                <span v-if="isValid.status == 'refused' "class="span span-error">{{ this.trans('user_provider_web.status_refused') }}</span>
                                <span v-if="isValid.status == 'fail' "class="span span-error">{{ this.trans('user_provider_web.status_fail') }}</span>
                                <span v-if="isValid.status == 'waiting_payment' "class="span span-waiting">{{ this.trans('user_provider_web.status_waiting') }}</span>
                            </div>
                            <div v-else>
                                <span 
                                    v-if="isValid.status == 'authorized' || isValid.status == 'paid' " 
                                    class="span span-success">
                                    {{ this.trans('user_provider_web.status_paid') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="container-link-payment" 
                        v-if="isValid.pix && isValid.pix.isValid && isValid.status == 'waiting_payment' ">
                        <a :href="`${this.urlPix}?id=${isValid.pix.transaction_id}`" target="__blank" class="link-payment">Clique aqui para realizar o pagamento</a>
                    </div>
                </div>
                <div
                    class="actual-plan" 
                    v-if="!isValid.is_valid && isValid.signature_id">
                    <p clas="title">{{ this.trans('user_provider_web.plan_expired') }}</p>
                </div>
                <div class="plans-grid">
                    <template v-if="planslist.length">
                        <plans 
                            v-if="plan.id != isValid.plan_id || 
                                (isValid.status != 'paid' && 
                                isValid.status != 'authorized' && 
                                isValid.status != 'waiting_payment') " 
                            v-for="plan in planslist" 
                            :key="plan.id" 
                            :plans="plan" />
                    </template>
                    <p v-else>{{ this.trans('user_provider_web.no_plans') }}</p>
                </div>
            </div>
            	
        </div>	
    </div>    
</template>

<script>
import plans from "./Plans.vue";

export default {
    props: [ 
        'planslist',
        'validsignature',
        'urlPix'
    ],
    components: {
        plans
    },
    data() {
        return {
            isValid: '',
            arrPlansList: ''
        }
    },
    computed: {
        planValidName() {
            if (this.isValid) {
                let id = this.isValid.plan_id
                let plan = this.arrPlansList = this.arrPlansList.filter((arr)=>arr.id === id )
                return plan[0].name
            }
        }
    },
    mounted() {
        this.isValid = this.validsignature
        this.arrPlansList = this.planslist
    }
}
</script>

<style>
    .plans-grid {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    .actual-plan {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    .card-plan {
        min-width: 300px;
    }

    .title {
        font-size: 22px;
        justify-content: flex-start;
        align-items: flex-start;
        display: flex;
        padding: 0;
        margin: 0;
    }
    .sub-title {
        font-size: 12px;
    }

    .link-payment {
        text-decoration: none;
        cursor: pointer;
        text-align: center;
    }

    .container-status {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .span {
        background-color: #8F8F8F;
        color: #FFF;
        padding-left: 5px;
        padding-right: 5px;
        padding-bottom: 2px;
        padding-top: 2px;
        border-radius: 5px;
        text-align: center;
    }

    .span-success {
        background-color: #149514 !important;
    }

    .span-error {
        background-color: #dd1a1a !important;
    }

    .span-info {
        background-color: #026ecb !important;
    }

    .span-waiting {
        background-color: #eb7f0f !important;
    }
</style>
