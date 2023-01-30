<template>
  <div>
    <!-- Row -->
    <div class="tab-content">
      <div class="col-lg-12">
        <div class="card card-outline-info">
          <div class="card-header">
            <h4 class="m-b-0 text-white">{{trans('plan.filters')}}</h4>
          </div>
          <div class="card-block">
            <div class="row">
              <div class="col-md-4 col-sm-12">
                <!--Id-->
                <div class="form-group">
                  <label for="id" class="control-label">{{ trans('plan.id') }}</label>
                  <input
                    v-model="plan_filter.id"
                    name="id"
                    type="number"
                    id="id"
                    class="form-control input-lg"
                    auto-focus
                    :placeholder="trans('plan.id')"
                  >
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <!--Name-->
                <div class="form-group">
                  <label for="name" class="control-label">{{ trans('plan.name') }}</label>
                  <input
                    v-model="plan_filter.name"
                    name="name"
                    type="text"
                    id="name"
                    class="form-control input-lg"
                    maxlenght="255"
                    auto-focus
                    :placeholder="trans('plan.name')"
                  >
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <!--Preço-->
                <div class="form-group">
                  <label for="plan_price" class="control-label">{{ trans('plan.plan_price') }}</label>
                  <input
                    v-model="plan_filter.plan_price"
                    name="plan_price"
                    type="text"
                    id="plan_price"
                    class="form-control"
                    auto-focus
                    :placeholder="trans('plan.plan_price')"
                  >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <!--Validade-->
                <div class="form-group">
                  <label for="validity" class="control-label">{{ trans('plan.validity') }}</label>
                  <input
                    v-model="plan_filter.validity"
                    name="validity"
                    type="text"
                    id="validity"
                    class="form-control input-lg"
                    auto-focus
                    :placeholder="trans('plan.validityInDays')"
                  >
                </div>
              </div>

              <div class="col-md-6 col-sm-12">
                <!--Quantidade de Assinaturas -->
                <div class="form-group">
                  <label
                    for="quantity_signatures"
                    class="control-label"
                  >{{ trans('plan.quantity') }}</label>
                  <input
                    v-model="plan_filter.quantity_signatures"
                    name="quantity_signatures"
                    type="number"
                    id="quantitiy_signatures"
                    class="form-control input-lg"
                    :placeholder="trans('plan.quantity_signatures')"
                  >
                </div>
              </div>
            </div>

            <!-- Action -->
            <div class="form-group">
              <button type="button" v-on:click="clearForm()" class="btn btn-danger">
                <i class="fa fa-trash"></i>
                {{trans('plan.clear_form')}}
              </button>
              <div class="pull-right">
                <div class="col-md-6 col-md-offset-4">
                  <button type="button" v-on:click="fetch()" class="btn btn-success">
                    <i class="fa fa-search"></i>
                    {{trans('plan.filter')}}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-content">
      <div class="col-lg-12">
        <div class="card card-outline-info">
          <div class="card-block">
            <table class="table">
              <!-- <th>{{trans('plan.id')}}</th> -->
              <th>
                <center>{{trans('plan.id')}}</center>
              </th>
              <th>
                <center>{{trans('plan.name')}}</center>
              </th>
              <th>
                <center>{{trans('plan.plan_price')}}</center>
              </th>
              <th>
                <center>{{trans('plan.validity')}}</center>
              </th>
              <th>
                <center>{{trans('plan.quantity_signatures')}}</center>
              </th>
              <th>
                <center>{{trans('plan.location')}}</center>
              </th>
              <th>
                <center>{{trans('plan.action')}}</center>
              </th>
              <tbody>
                <tr v-for="plan in plans.data" v-bind:key="plan.id">
                  <td>
                    <center>{{ plan.id }}</center>
                  </td>
                  <td>
                    <center>{{ plan.name }}</center>
                  </td>
                  <td>
                    <center>{{ formatCurrency(plan.plan_price) }}</center>
                  </td>
                  <td>
                    <center>{{ plan.validity}}</center>
                  </td>
                  <td>
                    <center>{{ plan.quantity}}</center>
                  </td>
                  <td>
                    <center>{{ plan.location_name }}</center>
                  </td>
                  <td>
                    <div class="dropdown">
                      <center>
                        <button
                          class="btn btn-info dropdown-toggle"
                          type="button"
                          id="dropdownMenu1"
                          data-toggle="dropdown"
                        >
                          {{trans('plan.action_grid') }}
                          <span class="caret"></span>
                        </button>

                        <div
                          class="dropdown-menu dropdown-menu-right"
                          role="menu"
                          aria-labelledby="dropdownMenu1"
                        >
                          <!-- EDITAR -->
                          <a
                            v-if="EditPermission"
                            class="dropdown-item"
                            tabindex="-1"
                            :href="'/admin/plan/' + plan.id"
                          >{{ trans('plan.edit') }}</a>

                          <!-- DELETAR -->
                          <button
                            v-if="DeletePermission"
                            v-on:click="deletePlan(plan.id, plan.name)"
                            type="button"
                            class="dropdown-item"
                            tabindex="-1"
                          >{{trans('plan.delete')}}</button>
                        </div>
                      </center>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <pagination :data="plans" @pagination-change-page="fetch"></pagination>
      </div>
    </div>
    <!-- Row -->
  </div>
</template>
<script>
import axios from "axios";

export default {
  props: ["EditPermission", "DeletePermission", "Plans", "CurrencySymbol"],
  data() {
    return {
      plans: [],
      currency: '$',
      qtd: [],
      plan_filter: {
        name: "",
        plan_price: "",
        validity: "",
        id: "",
        quantity_signatures: ""
      }
    };
  },
  methods: {
    fetch(page = 1) {
      var component = this;
      axios
        .post("/admin/plan/fetch", {
          pagination: {
            actual: page,
            itensPerPage: 10
          },
          filters: {
            Plan: this.plan_filter,
            ItensPerPage: 10
          }
        })
        .then(
          response => {
            console.log("response", response.data);
            component.plans = response.data.plans;
            component.qtd = response.data.qtds;
          },
          response => {
            // error callback
          }
        );
      this.$nextTick();
    },
    clearForm() {
      this.plan_filter.name = "";
      this.plan_filter.plan_price = "";
      this.plan_filter.validity = "";
      this.plan_filter.id = "";
      this.plan_filter.quantity_signatures = "";
    },
    formatCurrency(value) {
      if (value != undefined || value != "") {                
          let val = (value/1).toFixed(2).replace('.', ',')
          return this.currency + " " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
      } else {
          return "";
      }
    },
    deletePlan(id, name) {
      this.$swal({
        title: this.trans("plan.plan_delete_confirm") + " " + name + "?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.trans("plan.yes"),
        cancelButtonText: this.trans("plan.no")
      }).then(result => {
        if (result.value) {
          //Try to remove and show fail mission if fails
          axios.post("/admin/plan/delete/" + id).then(
            response => {
              if ((response = "success")) {
                this.$swal({
                  title: this.trans("plan.success_delete"),
                  type: "success"
                });
                location.reload();
              } else {
                this.$swal({
                  title: this.trans("plan.delete_failed"),
                  type: "error"
                });
              }
            },
            response => {
              console.log(response);
              // error callback
            }
          );
        }
      });
    }
  },
  created() {
    this.plans = JSON.parse(this.Plans);
    if(this.CurrencySymbol) {
      this.currency = this.CurrencySymbol;
    }
    // this.fetch();
  }
};
</script>