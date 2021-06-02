<template>
    <div class="col-lg-12">
        <div class="card card-outline-info">
            
            <div class="card-header text-center">
                <h1 class="text-white m-b-0">{{ trans('user_provider_web.acquire') + plan.name }}</h1>
            </div>

            <div class="card-block">
                <div class="plan-detail">
                    <p><strong>{{ trans('user_provider_web.plan') + plan.name }}</strong></p>
                    <p>{{ trans('user_provider_web.period') + plan.period + ' ' + trans('user_provider_web.days') }}</p>
                    <p>{{ trans('user_provider_web.value') + formatMoney(plan.plan_price) }}</p>
                </div>
                    <div class="box">
                        <p>{{ trans('user_provider_web.select_charge_type') }}</p>
                    <select v-model="charge_type">
                        <option disabled value="">{{ trans('user_provider_web.select') }}</option>
                        <option v-for="data in getPayments" :key="data.charge_type" :value="data.charge_type">{{ data.name }}</option>
                    </select>
                </div>

                <div class="box" v-if="'card' === charge_type">
                    <div class="add-card">
                        <p>{{ trans('user_provider_web.add_card') + ':' }}</p>
                        <button type="button" data-toggle="modal" data-target="#modalCard" class="btn btn-info button-card">{{ trans('user_provider_web.btn_add') }}</button>
                    </div>
                    <div class="plan-detail-card">
                        <div class="box">
                            <p>{{ trans('user_provider_web.select_card') }}</p>
                            <select v-model="payment_id">
                                <option disabled value="">{{ trans('user_provider_web.select') }}</option>
                                <option v-for="data in allCards" :key="data.id" :value="data.id">{{ data.card_type + ' **** ' + data.last_four}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button v-else type="button" style="margin-left: 75%" class="btn btn-success col-sm-3" data-toggle="modal" data-target="#myModal">{{ trans('user_provider_web.confirm_signature') }}</button>  
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" role="dialog">
            <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans('user_provider_web.confirm_signature') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>{{ trans('user_provider_web.are_you_sure') }}</p>
                    <p>{{ plan.name }}</p>
                    <p>{{ trans('user_provider_web.period') + ' ' + plan.period + ' ' + trans('user_provider_web.days') }}</p>
                    <p>{{ trans('user_provider_web.value') + ' ' + formatMoney(plan.plan_price) }}</p>
                </div>
                <div class="modal-footer">
                    <button v-if="!onLoad" type="button" class="btn btn-success" @click="saveSignature">{{ trans('user_provider_web.assign_plan') }}</button>
                    <button v-else type="submit" class="btn btn-success m-progress">{{ trans('user_provider_web.assign_plan') }}</button>
                </div>
            </div>
            </div>
        </div>

        <AddCardModal :id="provider_id" />	
    </div> 
</template>

<script>
import axios from 'axios'
import AddCardModal from './AddCardModal'

export default {
    components: {
        AddCardModal
    },
    props: [
        'plan',
        'provider',
        'payment'
    ],
    data() {
        return {
            payments: [],
            allCards: [],
            plan_id: '',
            provider_id: '',
            payment_id: '',
            charge_type: '',
            onLoad: false
        }
    },
    methods: {
        formatMoney(number) {
            return number.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })
        },
        /**
         * Gera uma assinatura do plano 
         */
        saveSignature() {
            this.onLoad = true

            axios.post('/libs/provider/plan/updatePlan', {
                plan_id: this.plan_id,
                provider_id: this.provider_id,
                payment_id: this.payment_id,
                charge_type: this.charge_type,
            })
            .then(response => {
                this.onLoad = false
                jQuery("#myModal").modal("hide");

                if (response.data.success) {
                    this.$swal({
                        type: 'success',
                        title: 'OK!',
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
    computed: {
        getPayments() {
            return this.payments;
        }
    },
    created() {
        this.plan_id = this.plan.id 
        this.provider_id = this.provider.id
        this.allCards = this.payment
        this.payments = [
            {
                charge_type: 'billet',
                name: this.trans('payment.billet')
            },
            { 
                charge_type: 'card',
                name: this.trans('payment.credit_card')
            }
        ]
        this.$eventBus.$on('send-data', (data) => {
            this.allCards.push(data)
        });
    }
}
</script>

<style scoped>
    .card-header {
        padding-top: 60px;
        padding-bottom: 60px;
    }

    .card-header h1 {
        font-size: 42px;
    }

    .card-block {
        display: flex;
        justify-content: center;
        flex-direction: column;
    }

    .plan-detail {
        align-self: center;
        width: 100%;
        min-width: 500px;
        border: 1px solid #D9DADC;
        border-radius: 5px;
        background: #F8F8F8;
        margin-bottom: 20px;
        padding: 25px;
    }

    .plan-detail p {
        margin-bottom: 0;
    }

    .plan-detail-card {
        align-self: center;
        width: 100%;
        height: 250px;
        min-width: 500px;
        margin-bottom: 20px;
        border: none;
        border-radius: 5px;
        padding: 25px;

        box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.15); 
    }


    .box select {
        background-color: #fff;
        color: #000;
        padding: 12px;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 20px;
        outline: none;
    }

    .box select option {
        padding: 30px;
    }

    .plan-detail-card .button-confirm {
        cursor: pointer;

        text-transform: uppercase;
        letter-spacing: 1px;

        float: right;
    }

    .plan-detail-card .button-confirm:hover {
        background: #00cc00;
    }

    .plan-detail-card .button-confirm:focus {
        outline: 0;
    }

    .button-card {
        float: right;
    }

    .add-card {
        align-self: center;
        width: 100%;
        min-width: 500px;
        margin-bottom: 20px;
        border: none;
        border-radius: 5px;
        padding: 25px;

        box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.15); 
    }

    .add-card p {
        display: inline;
    }
</style>
