<template>
<div class="text-center">
      <v-card>
        <v-card-title class="headline grey lighten-2">
          {{ action == 'store' ? 'Novo lote' : 'Editar lote' }}
        </v-card-title>

        <v-card-text>
          <v-container>
              <v-row v-if="errors">
                  <v-col cols="12">
                      <v-alert
                        dense
                        border="left"
                        type="warning"
                        v-for="error in errors"
                        :key="error"
                        >
                        {{ error }}
                        </v-alert>
                  </v-col>
              </v-row>
            <v-row>
              <v-col cols="12">
                <v-text-field label="Número do lote *" required v-model="number"></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-divider></v-divider>
        <v-btn color="primary" @click="dialog = true">Vincular operadora</v-btn>
        <v-simple-table
            fixed-header
            height="200"
        >
        <template v-slot:default>
        <thead>
          <tr>
            <th class="text-left">Operadora</th>
            <th class="text-left">Excluir</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in lot_operators" :key="item.operator_id">
            <td>{{ item.operator }}</td>
            <td><v-icon @click="remove(item)">mdi-delete</v-icon></td>
          </tr>
        </tbody>
      </template>
        </v-simple-table>
        <v-dialog
      v-model="dialog"
      width="500"
    >
        <dialog-lot-operator v-if="dialog" :operators="operators" :operators_lot="lot_operators" @action="save"></dialog-lot-operator>
    </v-dialog>
        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="$emit('action')">Fechar</v-btn>
          <v-btn color="blue darken-1" text @click="salvar">Salvar</v-btn>
        </v-card-actions>
      </v-card>
  </div>
</template>

<script>
import DialogLotOperator from './DialogLotOperator'
export default {
    name: 'dialog-lot',
    components: {
        DialogLotOperator
    },
    props: {
        lot: {
            type: Object,
            default: () => ({})
        },
        operators: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            dialog: false,
            number: '',
            action: 'store',
            errors: []
        }
    },
    computed: {
        lot_operators() {
            return this.lot.operators.map(lot_op => {
                lot_op.lot_operator.operator = lot_op.name;
                return lot_op.lot_operator;
            })
        }
    },
    created(){
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if(this.lot.id){
                this.number = this.lot.number,
                this.action = 'update'
            } else {
                this.action = 'store'
            }
        },
        salvar() {
            this.errors = [];
            if(this.action == 'store') {
                this.request().post('/lots/store', {
                    number: this.number,
                    operators: this.lot_operators
                }).then(response => {
                    this.$emit('action')
                }).catch(err => {
                    if(err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            } else {
                this.request().put(`/lots/${this.lot.id}/update`, {
                    number: this.number,
                    operators: this.lot_operators
                }).then(response => {
                    this.$emit('action')
                }).catch(err => {
                    if(err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            }
        },
        save(data) {
            if(data && data.action == 'store'){
                this.lot.operators.push({
                        name: data.operator.text,
                        lot_operator: {
                            operator_id: data.operator.value,
                            lot_id: this.lot.id
                        }
                    })
            }
            this.dialog = false;
        },
        remove(data) {
            this.lot.operators.splice(this.lot.operators.findIndex(lot_op => lot_op.lot_operator.operator_id == data.operator_id && lot_op.lot_operator.lot_id == this.lot.id), 1);
        }
    }
}
</script>

<style>

</style>
