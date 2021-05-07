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
    </div>    
</template>

<script>
import plans from "./Plans.vue";

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
</style>
