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
                        <p>{{ this.trans('user_provider_web.expiry_in') + isValid.next_expiration }}</p>
                        <div v-if="isValid.is_cancelled">
                            <p class="text-danger">{{ this.trans('signature.signature_cancelation') }}</p>
                        </div>
                        <button v-else data-toggle="modal" data-target="#myModal" class="btn btn-inverse btn-flat btn-block" >
                            {{trans('user_provider_web.cancel') }}
                        </button>
                    </div>
                </div>
                <div class="plans-grid">
                    <template v-if="planslist.length">
                        <plans v-for="plan in planslist" :key="plan.id" :plans="plan" />
                    </template>
                    <p v-else>{{ this.trans('user_provider_web.no_plans') }}</p>
                </div>
            </div>
            	
        </div>	
        
        <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('user_provider_web.cancel') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>{{ trans('user_provider_web.are_you_sure_cancel') }}</p>
                </div>
                <div class="modal-footer">
                    <button v-if="!onLoad" type="button" class="btn btn-success" @click="cancelPlan">{{ trans('user_provider_web.cancel') }}</button>
                    <button v-else type="submit" class="btn btn-success m-progress">{{ trans('user_provider_web.cancel') }}</button>
                </div>
            </div>
            </div>
        </div>
    </div>    
</template>

<script>
import plans from "./Plans.vue";
import axios from 'axios'

export default {
    props: [ 
        'planslist',
        'validsignature'
    ],
    components: {
        plans
    },
    data() {
        return {
            onLoad: false,
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
    methods: {
        cancelPlan() {
            this.onLoad = true

            axios.post('/libs/provider/cancel_subscription', {
                subscription_id: this.isValid.signature_id
            })
            .then(response => {
                this.onLoad = false
                jQuery("#myModal").modal("hide");

                if (response.data.success) {
                    this.$swal({
                        type: 'success',
                        title: this.trans('signature.signature_cancelation'),
                        text: response.data.message
                    })
                } else {
                    this.$swal({
                        type: 'error',
                        title: 'Error!',
                        text: response.data.message
                    })
                }
            })
            .catch(error => {
                this.onLoad = false
                jQuery("#myModal").modal("hide");

                this.$swal({
                    type: 'error',
                    title: 'Error!'
                })
                console.log(error);
            })
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
</style>
