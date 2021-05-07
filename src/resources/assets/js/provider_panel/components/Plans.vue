<template>
    <div>
        <!-- Plan card -->
        <div class="plans">
            <h1>{{ plans.name }}</h1>
            <p>{{ trans('user_provider_web.period') + ' ' + plans.period + ' ' + trans('user_provider_web.days') }}</p>
            <p>{{ trans('user_provider_web.value') + ' ' + formatMoney(plans.plan_price) }}</p>
            <button type="button" :onclick="`window.location.href='plan/${plan_id}'`">{{ trans('user_provider_web.assign_plan') }}</button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: [
        'plans'
    ],
    data() {
        return {
            plan_id: ''
        }
    },
    methods: {
        formatMoney(number) {
            return number.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })
        }
    },
    created() {
        this.plan_id = this.plans.id
    }
}
</script>

<style scoped>
    .plans {
        font-family: "Rubik",sans-serif;
        box-sizing: border-box;
        background: #FFF;
        width: 300px;
        margin: 10px 16px;
        padding: 18px;
        border-radius: 8px;
        border: none;

        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .plans h1 {
        color: #009efb;
        font-weight: 300;
        text-align: center;
        font-size: 48px;
        line-height: 48px;

        padding-top: 0; 
        padding-right: 15px; 
        padding-bottom: 46px; 
        padding-left: 15px;
    }

    .plans p {
        padding-top: 0; 
        padding-right: 15px; 
        padding-bottom: 0; 
        padding-left: 15px;

        margin-bottom: 0;  
    }

    .plans button {
        background: #1DB954;
        color: #FFF;
        width: 100%;
        padding-top: 16px; 
        padding-bottom: 16px; 
        margin-top: 18px;
        cursor: pointer;

        border: none;
        border-radius: 50px;

        font-size: 16px;
        font-weight: 500;

        text-transform: uppercase;
        letter-spacing: 1px;

        transition: all .2s ease-in-out;    
    }

    .plans button:hover {
        transform: scale(1.1);
    }

    .plans button:focus {
        outline: 0;
    }
</style>
