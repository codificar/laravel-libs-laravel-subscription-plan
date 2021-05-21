<script>
import axios from "axios";
import VuePassword from 'vue-password';
import { required } from 'vuelidate/lib/validators';

export default {
    props: ["redirect", "edit", "crsf_token", "Plan", 'Locations'],
    data() {
        return {
            plan: {
                //Object plan to be handled by Laravel controller
                id: "",
                name: "",
                period: "",
                validity: "",
                plan_price: "",
                client: "",
                visibility: 1,
                location: '',
                allow_cancelation: ''
            },
            locationsData: []
        };
    },

    components:{
      VuePassword
    },
    
    validations:{
      plan : {
          name : {
              required
          },
          period : {
              required
          },
          plan_price : {
              required
          },
          client : {
              required
          },
          visibility : {
              required
          }
      },
    plan_validation_group : ['plan']
    }, 
  
    methods: {
        clearForm(){
            this.plan = {
                name: "", 
                period: "", 
                validity: "",
                plan_price: "", 
                client: "",
                visibility: 1 
            }  
        },
        
        test() {
            console.log(this.plan.visibility)
        },

        submitForm: function(){
            
            this.$refs.submit_button.click();
            this.$v.plan_validation_group.$touch();

            if (this.plan.validity && this.plan.validity < this.plan.period) {
                this.$swal({
                    title: this.trans('plan.min_validity'),
                    type: 'warning'
                });

                return;
            }

            //Submit form if its valid
            if (!this.$v.plan_validation_group.$invalid){
                new Promise((resolve, reject) => {
                    axios.post('/admin/plan/store', {
                            plan: this.plan
                    }).then(
                            response => {
                                if (response.data){
                                    this.$swal({
                                        title: this.trans('plan.added_success'),
                                        type: 'success'
                                    }).then(result =>{
                                            window.location.pathname = this.redirect;
                                        }
                                    );
                                } else {
                                    this.$swal({
                                        title: this.trans('plan.added_fail'),
                                        html: '<label class="alert alert-danger alert-dismissable text-left">' +response.data.errors.join("<br>") + '</label>',
                                        type: 'error'
                                    });
                                }
                            }
                    ).catch(error => {
                        console.log(error);
                        reject(error);
                        return false;
                    });
                });
            } 
            else{ 
                this.$swal({
                    title: this.trans('plan.please_fill'),
                    type: 'warning'
                });
            }
        }
    },
    created() {
        this.locationsData = JSON.parse(this.Locations);

        if (this.edit) {
            let values =  JSON.parse(this.Plan);
            this.plan.id = values.id;
            this.plan.name = values.name;
            this.plan.period = values.period;
            this.plan.validity = values.validity;
            this.plan.plan_price = values.plan_price;
            this.plan.client = values.client;
            this.plan.visibility = values.visibility;
            this.plan.location = values.location;
            this.plan.allow_cancelation = values.allow_cancelation;
        }
    }
};

</script>
<template>
    <div>
        <div class="col-lg-12"> 
            <form data-toggle="validator" v-on:submit.prevent="" class="card card-outline-info">
                <div class="card-header">
                    <h4 v-if="edit == true" class="m-b-0 text-white">{{ trans('plan.plan_edit') }}</h4>
                    <h4 v-else class="m-b-0 text-white">{{ trans('plan.plan_add') }}</h4>
                </div>
                <br>
                <div class="box box-primary">			
                    <hr>
                </div>	
                <div class="card">                    
                    <div class="card-block">
                        <input type="hidden" name="_token" :value="crsf_token">
                        <div class="card">
                            <div id="plan" class="box-body">
                                <h3 class="card-title">{{ trans('plan.plan_info') }}</h3>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <!--Name-->
                                        <div class="form-group">
                                            <label for="plan_name" class=" control-label">{{ trans('plan.name') }}*</label>
                                            <input v-model="plan.name" required name="plan_name" type="text" id="plan_name" 
                                                class="form-control input-lg" maxlenght="255" minlenght="3" auto-focus="" 
                                                :placeholder="trans('plan.name')" >
                                            <div class="help-block with-errors"></div>	
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <!--Period-->
                                        <div class="form-group">
                                            <label for="period" class=" control-label">{{ trans('plan.period') }}*</label>
                                            <select v-model="plan.period" required name="plan_period" type="text" id="plan_period" 
                                                class="form-control input-lg" auto-focus = "" 
                                                :placeholder="trans('plan.period')" >
                                                <option disabled value="">Escolha um item</option>
                                                <option value=1>{{trans('plan.daily_period')}}</option>
                                                <option value=7>{{trans('plan.weekly_period')}}</option>
                                                <option value=15>{{trans('plan.biweekly_period')}}</option>
                                                <option value=30>{{trans('plan.monthly_period')}}</option>
                                                <option value=60>{{trans('plan.bimonthly_period')}}</option>
                                                <option value=90>{{trans('plan.trimonthly_period')}}</option>
                                                <option value=180>{{trans('plan.semiannual_period')}}</option>
                                                <option value=365>{{trans('plan.anual_period')}}</option>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <!--Validity-->
                                        <div class="form-group">
                                            <label for="plan_validity" class=" control-label">{{ trans('plan.validity') }}</label>
                                            <input v-model="plan.validity" name="plan_validity" type="text" id="plan_validity" 
                                                class="form-control input-lg" auto-focus="" 
                                                :placeholder="trans('plan.validityInDays')" >
                                            <div class="help-block with-errors"></div>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <!--Plan Price-->
                                        <div class="form-group">
                                            <label for="plan_price" class=" control-label">{{ trans('plan.plan_price') }}*</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon">{{ trans('payment.dollar_sign') }}</span>
                                                    <input v-model="plan.plan_price" required name="plan_price" type="text" id="plan_price" 
                                                    class="form-control input-lg" auto-focus="" 
                                                    :placeholder="trans('plan.plan_price')" >
                                                </div>
                                            <div class="help-block with-errors"></div>	
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <!--Client-->
                                        <div class="form-group">
                                            <label for="client" class=" control-label">{{ trans('plan.client') }}*</label>
                                            <div class="radio-group">
                                                <input v-model="plan.client" required name="client" type="radio" 
                                                    id="client" value="Provider" auto-focus="" >
                                                <label for = "client">{{trans('plan.provider')}}</label>
                                            <div class="help-block with-errors"></div>	
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <!--Client-->
                                        <div class="form-group">
                                            <label for="client" class=" control-label">{{ trans('plan.plan_visibility') }}</label>
                                            <div class="radio-group">
                                                <input v-model="plan.visibility" name="visibility" type="radio" value="1" auto-focus="">
                                                <label for="visibility">{{ trans('plan.visible') }}</label>
                                                
                                                <input v-model="plan.visibility" name="visibility" type="radio" value="0" auto-focus="" >
                                                <label for="visibility">{{ trans('plan.invisible') }}</label>
                                            <div class="help-block with-errors"></div>	
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <!--Location-->
                                    <div class="form-group">
                                        <label for="location" class=" control-label">{{ trans('plan.location') }}*</label>
                                        <select type="text" v-model="plan.location" name="location"
                                            class="form-control input-lg" auto-focus = "" 
                                            :placeholder="trans('plan.period')" >
                                            <option value="">Escolha um item</option>
                                            <option v-for="item in locationsData" :key="item.id" :value="item.id">{{ item.name }}</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <!--Location-->
                                    <div class="form-group">
                                        <label for="location" class=" control-label">{{ trans('plan.allow_cancelation') }}*</label>
                                        <select type="text" v-model="plan.allow_cancelation" name="allow_cancelation"
                                            class="form-control input-lg" auto-focus = "" 
                                            :placeholder="trans('plan.allow_cancelation')" >
                                            <option value="1">Sim</option>
                                            <option value="0">Não</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="box-footer">					
                                <div class="pull-right">
                                    <div class="row">
                                        <div class="col">
                                            <button v-if="edit != true" v-on:click="clearForm()" type="button" class="btn btn-inverse">
                                                {{trans('plan.reset')}}
                                            </button>								
                                        </div>
                                        <div class="col">
                                            <button v-on:click="submitForm()" type="button" class="btn btn-success">
                                                {{trans('plan.send')}}
                                            </button>
                                        </div>
                                        <button ref="submit_button" type="submit" class="btn btn-success" style="display: none">
                                                {{trans('plan.send')}}
                                            </button>
                                    </div>
                                </div>
                            </div>	
                        </div>      
                    </div>		
                </div>
            </form>

        </div>
        <!-- Row -->
    </div>
</template>

<style>
.radio-group label {
    margin-right: 10px;
}
</style>
