<script>
import axios from "axios";

export default {
  props: ["EditPermission", "DeletePermission", "Signatures"],
  data() {
    return {
      signatures: [],

      signature_filter: {
        id: "",
        name: "",
        client_name: "",
        signature_price: "",
        initial_date: "",
        expiration_date: ""
      }
    };
  },
  methods: {
    suspendOrActivate(id) {
      this.$swal({
        title: this.trans("signature.signature_active_confirm"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.trans("signature.yes"),
        cancelButtonText: this.trans("signature.no")
      }).then(result => {
        if (result.value) {
          //Try to remove and show fail mission if fails
          axios.post("/admin/signature/suspend/" + id).then(
            response => {
              if ((response = "success")) {
                this.$swal({
                  title: this.trans("signature.active_success"),
                  type: "success"
                });
                location.reload();
              } else {
                this.$swal({
                  title: this.trans("signature.active_failed"),
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
    },
    test() {
      console.log("Testando");
    },
    fetch(page = 1) {
      var component = this;
      axios
        .post("/admin/signature/fetch", {
          pagination: {
            actual: page,
            itensPerPage: 10
          },
          filters: {
            signatures: this.signature_filter,
            ItensPerPage: 10
          }
        })
        .then(
          response => {
            console.log("response", response.data);
            component.signatures = response.data.signatures;
          },
          response => {
            // error callback
          }
        );
      this.$nextTick();
    },
    clearForm() {
      this.signature_filter.name = "";
      this.signature_filter.signature_price = "";
      this.signature_filter.validity = "";
      this.signature_filter.id = "";
      this.signature_filter.quantity_signatures = "";
    },
    deletePlan(id, name) {
      this.$swal({
        title:
          this.trans("signature.signature_delete_confirm") + " " + name + "?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.trans("signature.yes"),
        cancelButtonText: this.trans("signature.no")
      }).then(result => {
        if (result.value) {
          //Try to remove and show fail mission if fails
          axios.post("/admin/plan/delete/" + id).then(
            response => {
              if ((response = "success")) {
                this.$swal({
                  title: this.trans("signature.success_delete"),
                  type: "success"
                });
                location.reload();
              } else {
                this.$swal({
                  title: this.trans("signature.delete_failed"),
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
    this.signatures = JSON.parse(this.Signatures);
    // this.fetch();
  }
};
</script>
<template>
  <div>
    <!-- Row -->
    <div class="tab-content">
      <div class="col-lg-12">
        <div class="card card-outline-info">
          <div class="card-header">
            <h4 class="m-b-0 text-white">{{trans('signature.filters')}}</h4>
          </div>
          <div class="card-block">
            <div class="row">
              <div class="col-md-3 col-sm-12">
                <!--Id-->
                <div class="form-group">
                  <label for="id" class="control-label">{{ trans('signature.id') }}</label>
                  <input
                    v-model="signature_filter.id"
                    name="id"
                    type="number"
                    id="id"
                    class="form-control input-lg"
                    auto-focus
                    :placeholder="trans('signature.id')"
                  >
                </div>
              </div>

              <div class="col-md-3 col-sm-12">
                <!--Name-->
                <div class="form-group">
                  <label for="signature_name" class="control-label">{{ trans('signature.name') }}</label>
                  <input
                    v-model="signature_filter.name"
                    name="name"
                    type="text"
                    id="signature_name"
                    class="form-control input-lg"
                    maxlenght="255"
                    auto-focus
                    :placeholder="trans('signature.name')"
                  >
                </div>
              </div>

              <div class="col-md-3 col-sm-12">
                <!--Client_Name-->
                <div class="form-group">
                  <label
                    for="provider_signature_name"
                    class="control-label"
                  >{{ trans('signature.client_name') }}</label>
                  <input
                    v-model="signature_filter.client_name"
                    name="client_signature_name"
                    type="text"
                    id="client_signature_name"
                    class="form-control input-lg"
                    maxlenght="255"
                    auto-focus
                    :placeholder="trans('signature.client_name')"
                  >
                </div>
              </div>

              <div class="col-md-3 col-sm-12">
                <!--Preço-->
                <div class="form-group">
                  <label
                    for="signature_price"
                    class="control-label"
                  >{{ trans('signature.signature_price') }}</label>
                  <input
                    v-model="signature_filter.signature_price"
                    name="signature_price"
                    type="text"
                    id="signature_price"
                    class="form-control"
                    auto-focus
                    :placeholder="trans('signature.signature_price')"
                  >
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <!--Data de Inscrição-->
                <div class="form-group">
                  <label
                    for="initial_date"
                    class="control-label"
                  >{{ trans('signature.initial_date') }}</label>
                  <input
                    v-model="signature_filter.initial_date"
                    name="initial_date"
                    type="text"
                    id="initial_date"
                    class="form-control input-lg"
                    auto-focus
                    :placeholder="trans('signature.initial_date')"
                  >
                </div>
              </div>

              <div class="col-md-6 col-sm-12">
                <!--Data de Vencimento-->
                <div class="form-group">
                  <label
                    for="expiration_date"
                    class="control-label"
                  >{{ trans('signature.expiration_date') }}</label>
                  <input
                    v-model="signature_filter.expiration_date"
                    name="expiration_date"
                    type="number"
                    id="expiration_date"
                    class="form-control input-lg"
                    :placeholder="trans('signature.expiration_date')"
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
                    {{trans('signature.filter')}}
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
              <th>
                <center>{{trans('signature.id')}}</center>
              </th>
              <th>
                <center>{{trans('signature.name')}}</center>
              </th>
              <th>
                <center>{{trans('signature.initial_date')}}</center>
              </th>
              <th>
                <center>{{trans('signature.expiration_date')}}</center>
              </th>
              <th>
                <center>{{trans('signature.signature_price')}}</center>
              </th>
              <th>
                <center>{{trans('signature.provider')}}</center>
              </th>
              <th>
                <center>{{trans('signature.situation')}}</center>
              </th>
              <th>
                <center>{{trans('signature.action')}}</center>
              </th>
              <tbody>
                <tr v-for="signature in signatures.data" v-bind:key="signature.id">
                  <td>
                    <center>{{ signature.id }}</center>
                  </td>
                  <td>
                    <center>{{ signature.name }}</center>
                  </td>
                  <td>
                    <center>{{ signature.created_at_formated || signature.created_at }}</center>
                  </td>
                  <td>
                    <center>{{ signature.next_expiration_formated || signature.next_expiration }}</center>
                  </td>
                  <td>
                    <center>{{ signature.plan_price }}</center>
                  </td>
                  <td>
                    <center>{{ signature.first_name }}</center>
                  </td>
                  <td>
                    <center>
                      <span
                        v-if="signature.activity == 1"
                        class="btn btn-success peq"
                      >{{trans('signature.active') }}</span>
                      <span
                        v-if="signature.activity == 0"
                        class="btn btn-danger peq"
                      >{{trans('signature.inactive') }}</span>
                    </center>
                    <!-- <center>{{ signature.activity }}</center> -->
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
                          {{trans('signature.action') }}
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
                            :href="'/admin/plan/' + signature.id"
                          >{{ trans('signature.debit_collection') }}</a>

                          <!-- ATIVAR DESATIVAR -->
                          <button
                            v-if="DeletePermission"
                            @click="suspendOrActivate(signature.id)"
                            type="button"
                            class="dropdown-item"
                            tabindex="-1"
                          >{{trans('signature.change_activity')}}</button>
                        </div>
                      </center>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <pagination :data="signatures" @pagination-change-page="fetch"></pagination>
      </div>
    </div>
    <!-- Row -->
  </div>
</template>