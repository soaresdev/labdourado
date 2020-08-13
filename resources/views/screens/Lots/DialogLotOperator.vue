<template>
<v-card>
        <v-card-title class="headline grey lighten-2">
          Vincular Operadora
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
                  <v-col class="d-flex" cols="12">
                <v-select
                v-model="operator"
                :items="operators_select"
                filled
                label="Operadora *"
                :item-text="operators_select.text"
                :item-value="operators_select.value"
                required
                ></v-select>
              </v-col>
              </v-row>
        </v-container>
        </v-card-text>
              <v-divider></v-divider>
              <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="$emit('action')">Fechar</v-btn>
          <v-btn color="blue darken-1" text @click="salvar">Salvar</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: 'dialog-lot-operator',
    props: {
        operators: {
            type: Array,
            default: () => ([])
        },
        operators_lot: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            action: 'store',
            operator: '',
            errors: []
        }
    },
    computed: {
        operators_select(){
            if(this.operators_lot.length > 0) {
                return this.operators.filter(op => !this.operators_lot.find(op_lot => op.value == op_lot.operator_id))
            } else {
                return this.operators;
            }
        }
    },
    methods: {
        salvar() {
            this.errors = []
            if(!this.operator) {
                this.errors.push('Preencha todos os campos!')
            } else {
            this.$emit('action', {
                action: this.action,
                operator: {
                    value: this.operator,
                    text: this.operators.find(op => op.value == this.operator).text
                }
            });
            }
        }
    }
}
</script>

<style>

</style>
