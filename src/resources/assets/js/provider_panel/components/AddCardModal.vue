<template>
    <!-- Modal -->
    <div class="modal fade" id="modalCard" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ trans("user_provider_web.add_card") }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body card-container">
                    <div class="card-wrapper"></div>

                    <div>
                        <form v-on:submit.prevent id="add-card">
                            <div class="input-row">
                                <div class="input-box">
                                    <input :placeholder="trans('user_provider_web.card_number')" type="text" name="number" autocomplete="off" v-model="card_number">
                                </div>
                                <div class="input-box">
                                    <input :placeholder="trans('user_provider_web.card_holder')" type="text" name="name" v-model="card_holder">
                                </div>
                            </div>
                            <div class="input-row">
                                <div class="input-box2">
                                    <input placeholder="MM/YYYY" type="tel" name="expiry" v-model="card_expiry">
                                </div>
                                <div class="input-box2">
                                    <input placeholder="CVC" type="number" name="cvc" v-model="card_cvc">
                                </div>  
                            </div>
                            <div class="button-box">
                                <button v-if="!onLoad" type="button" class="btn btn-success" @click="addNewCard">{{ trans("user_provider_web.btn_add") }}</button>
                                <button v-else type="submit" class="btn btn-success m-progress">{{ trans("user_provider_web.btn_add") }}</button>
                            </div>
                        </form>
                    </div>

                    <div class="clear"></div>   
                </div>
            </div>
        </div>
    </div>
    
</template>

<script>
import axios from 'axios'

export default {
    props: [
        'id'
    ],
    data() {
        return {
            card_number: '',
            card_holder: '',
            card_expiry: '',
            card_cvc: '',
            onLoad: ''
        }
    },
    methods: {
        /**
         * Adiciona um novo cartão de crédito
         */
        addNewCard() {
            this.onLoad = true
            axios.post('/api/v3/addCard', {
                provider: true,
                number: this.card_number,
                name: this.card_holder,
                cvc: this.card_cvc,
                expiry: this.card_expiry.replace(/\s/g, ''),
                'user-id': this.id,
            })
            .then(response => {
                this.onLoad = false
                jQuery("#modalCard").modal("hide");
                
                if (response.data.success) {
                    let newCard = response.data.payment

                    this.$swal({
                        type: 'success',
                        title: 'OK!',
                        text: response.data.message
                    })

                    this.$eventBus.$emit('send-data', newCard)
                } else {
                    this.$swal({
                        type: 'error',
                        title: 'Error!',
                        text: response.data.message
                    })
                }
            })
            .catch(error => {
                console.log(error)
            })
        }
    },
    mounted() {
        window.addEventListener("load", function(event) {
	        var card = new Card({ 
                form: document.querySelector('#add-card'),
                 container: '.card-wrapper'
            });
        });
    }
}
</script>

<style scoped>
    .modal {
        width: 50%;
        min-width: 360px;
        margin: 0 auto;
    }

    .card-container{
        box-sizing: border-box;
        width: 50%;
        min-width: 360px;
        margin: 0 auto;
        padding: 25px;
        padding-bottom: 12px;
    }

    .input-row {
        width: 100%;
    }

    .input-box {
        margin-top: 10px;
    }

    .input-box input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px; 
    }

    .input-box2 {
        margin-top: 10px;
    }

    .input-box2 input {
        width: 47%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        float: left;
    }

    input[name=expiry] {
        margin-right: 6%;
    }

    .button-box button {
        margin-top: 12px;
        float: right;
    }
</style>
