<template>
    <div class="col-lg-12">
        <div class="card card-outline-info">
            
            <div class="card-header">
                <h4 class="text-white m-b-0">{{ trans("user_provider_web.credit_card") }}</h4>
            </div>

            <div class="card-block">
                <table v-if="allCards.length > 0" class="card-table">
                    <tr>
                        <th>{{ trans("user_provider_web.card_situation") }}</th>
                        <th>{{ trans("user_provider_web.card_type") }}</th>
                        <th>{{ trans("user_provider_web.last_four") }}</th>
                        <th>{{ trans("user_provider_web.card_actions") }}</th>
                    </tr>
                    <tr v-for="(card, index) in allCards" :key="index">
                        <td>{{ card.is_default == 1 ? trans("user_provider_web.card_situation_default") : trans("user_provider_web.card_situation_normal") }}</td>
                        <td>{{ card.card_type }}</td>
                        <td>{{ card.last_four }}</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ trans("user_provider_web.card_actions") }}
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" @click="setDefaultCard(card.id, index)">{{ trans("user_provider_web.set_default") }}</a>
                                    <a class="dropdown-item delete" href="#" @click="removeCard(card.id)">{{ trans("user_provider_web.delete_card") }}</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <p v-else>{{ trans("user_provider_web.no_card") }}</p>

                <div class="add-card">
                    <button type="button" data-toggle="modal" data-target="#modalCard" class="btn btn-success">{{ trans("user_provider_web.add_card") }}</button>
                </div>
            </div>
            	
        </div>

        <AddCardModal :id="providerid" />
    </div>
</template>

<script>
import AddCardModal from './AddCardModal'
import axios from 'axios'

export default {
    props: [
        'providerid', 'cards'
    ],
    components: {
        AddCardModal
    },
    data() {
        return {
            allCards: [],
        }
    },
    methods: {
        /**
         * Seleciona um cartão como padrão
         */
       setDefaultCard(cardId, index) {
            axios.post('/api/defaultCard', {
                provider: true,
                userId: this.providerid,
                cardId: cardId
            })
            .then(response => {
                if (response.data.success) {
                    this.$swal({
                        type: 'success',
                        title: 'OK!',
                        text: response.data.message
                    })
                    
                    this.allCards.filter((arr) => arr.id === response.data.oldDefault)[0].is_default = 0
                    this.allCards[index].is_default = 1
                } else {
                    this.$swal({
                        type: 'error',
                        title: 'Erro!',
                        text: response.data.message
                    })
                }
            })
            .catch(error => {
                console.log(error)
            })
        },
        /**
         * Deleta um cartão
         */
        removeCard(cardId) {
            this.$swal({
                title: this.trans("user_provider_web.sure"),
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonColor: this.trans("user_provider_web.cancel"),
                confirmButtonText: this.trans("user_provider_web.yes")
            }).then((result) => {
                if (result.value) {
                    axios.post('/api/removeCard', {
                        provider: true,
                        userId: this.providerid,
                        cardId: cardId
                    })
                    .then(response => {
                        if (response.data.success) {
                            this.allCards = this.allCards.filter((arr)=>arr.id !== cardId )

                            this.$swal({
                                type: 'success',
                                title: 'OK!',
                                text: response.data.message
                            })
                        } else {
                            this.$swal({
                                type: 'error',
                                title: 'Erro!',
                                text: response.data.message
                            })
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    })
                }
            })
            
        }
    },
    created() {
        this.allCards = this.cards

        this.$eventBus.$on('send-data', (data) => {
            this.allCards.push(data)
        });
    }
}
</script>

<style scoped>
.card-table {
    width: 100%;
    border: 1px solid #eee;
}

.card-table th {
    text-align: center;
    width: 25%;
    font-weight: 500;
    padding: 12px 0;

}

.card-table td {
    text-align: center;
    width: 25%;
    padding: 12px 0;
    border: 1px solid #eee;
}

.add-card {
    margin-top: 22px;
}

.add-card button {
    float: right;
}

.delete {
    color: red;
}

.card-block p {
    text-align: center;
}

</style>
