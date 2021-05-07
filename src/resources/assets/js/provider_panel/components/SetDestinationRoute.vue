<template>
    <div class="col-lg-12" v-if="!this.iswebview">
        <div class="card card-outline-info">
            
            <div class="card-header">
                <h4 class="text-white m-b-0">{{ trans('providerDestination.chose_destination') }}</h4>
            </div>

            <div class="card-block">
                <div>
                    <div class="form-group">
                        <label for="usr"> {{ trans('providerDestination.chose_destination') }} </label>
                        <input @keyup="search" class="form-control" type="text" ref="autocomplete" :placeholder="placeholder" onfocus="value = ''" />
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for=""> {{ trans('providerDestination.active_route') }} </label>
                        <select class="form-control" v-model="onDestination">
                            <option value="1"> {{ trans('providerDestination.yes') }} </option>
                            <option value="0"> {{ trans('providerDestination.no') }} </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group text-right button-save">
                <button @click="changeActive" type="button" class="btn btn-success" >
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"> {{ trans('providerDestination.save_data') }} </span>
                </button>
            </div>
    	
        </div>
    </div>

    <div class="wv-container" v-else>

        <div>

            <div class="wv-title">
                <h1>{{ trans('providerDestination.chose_destination') }}</h1>
            </div>

            <div class="wv-form">
                <div>
                    <label for="search"> {{ trans('providerDestination.chose_destination') }} </label>
                </div>
                <input @keyup="search" class="form-control" type="text" ref="autocomplete" :placeholder="placeholder" onfocus="value = ''" />
            </div>

            <div class="wv-form">
                
                    <label for=""> {{ trans('providerDestination.active_route') }} </label>
                    <select class="form-control" v-model="onDestination">
                        <option value="1"> {{ trans('providerDestination.yes') }} </option>
                        <option value="0"> {{ trans('providerDestination.no') }} </option>
                    </select>
                
            </div>
            

        </div>

        <div class="form-group text-right wv-button" >
            <button @click="changeActive" type="button" class="btn btn-info">
                <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
                    {{ trans('providerDestination.save_data') }}
                </span>
            </button>
        </div>

    </div>
</template>


<script>
import axios from 'axios'

export default {

    props: ['iswebview', 'provider'],
    data () {
        return {
            onDestination: 0,
            instance: 0,
            placeholder: this.trans('providerDestination.search'),
            lat: null,
            lon: null
        }
    },
    methods: {
        async changeActive () {

            await this.getCoords ()
            await axios.post('/provider/active_destination', {
                id: this.provider.id,
                token: this.provider.token,
                destination_address: this.$refs.autocomplete.value,
                on_destination: this.onDestination,
                lat: this.lat,
                lon: this.lon
            })
            .then( res => {
                if ( res.data.success ) {
                    this.$swal({
                        type: 'success',
                        title: 'OK!',
                        text: this.trans('providerDestination.success_msg')
                    })
                } else {
                    var erro = ''
                    if ('errors' in res.data) {
                        erro = res.data.errors
                    }
                    this.$swal({
                        type: 'error',
                        title: 'Erro!',
                        text: erro
                    })
                }
            })
            .catch(err => {
                this.$swal({
                    type: 'error',
                    title: 'Erro!'
                })
            }) 

        },
        search () {
            if ( this.instance == 0) {

                this.autocomplete = new google.maps.places.Autocomplete(
                    (this.$refs.autocomplete),
                    {types: ['geocode']}
                );

                this.instance = 1
            }
        },
        async getCoords () {
            await axios.get('/provider/get_coords_address', { 
                params: {
                    id: this.provider.id,
                    token: this.provider.token,
                    address: this.$refs.autocomplete.value
                }
            })
            .then(res => {
                var { data } = res
                this.lat = data.latitude
                this.lon = data.longitude
            })
            .catch(() => {
                
            }) 
        }
    },
    mounted () {
        this.$refs.autocomplete.value = this.provider.destination_address
        this.onDestination = this.provider.on_destination      
    }

}
</script>

<style>

.card-block {
    display: flex;
    justify-content: center;
}

.card-block div {
    width: 100%;
    padding: 10px;
}

.button-save {
    padding-right: 40px;
}

.wv-container {
    width: 100%;
    max-width: 460px;
    margin: 0 auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
}

.wv-title {
    text-align: center;
}

.wv-form {
    margin-top: 12px;
    padding: 8px;
    background: rgb(245, 248, 250);
}

.wv-button {
    margin-top: 25px;
}

</style>