<template>
<div class="text-center">
<v-card>
        <v-card-title class="headline grey lighten-2">
          Vincular médico a operadora
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
              <v-col class="d-flex" cols="12"  md="6">
                <v-select
                v-model="operator"
                :items="operators_select"
                filled
                label="Operadora *"
                :item-text="operators.text"
                :item-value="operators.value"
                ></v-select>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="Código na operadora *" required v-model="doctor_operator_number"></v-text-field>
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
</div>
</template>

<script>
export default {
    name: 'dialog-doctor-operator',
    props: {
        doctor_operator: {
            type: Object,
        },
        operators: {
            type: Array,
            default: () => ([])
        },
        operators_doctor: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            doctor_operator_number: '',
            operator: '',
            action: 'store',
            errors: []
        }
    },
    created() {
        this.verify();
    },
    computed: {
        operators_select(){
            if(this.doctor_operator){
                return this.operators.filter(op => op.value === this.doctor_operator.operator_id)
            } else if(this.operators_doctor.length > 0) {
                return this.operators.filter(op => !this.operators_doctor.find(op_doc => op.value === op_doc.operator_id));
            } else {
                return this.operators;
            }
        }
    },
    methods:{
        verify(){
            if(this.doctor_operator){
                this.action = 'update';
                this.operator = this.doctor_operator.operator_id;
                this.doctor_operator_number = this.doctor_operator.doctor_operator_number;
            } else {
                this.action = 'store';
            }
        },
        salvar() {
            this.errors = []
            if(!this.operator || !this.doctor_operator_number) {
                this.errors.push('Preencha todos os campos!')
            } else {
                this.$emit('action', {
                action: this.action,
                operator: {
                    value: this.operator,
                    text: this.operators.find(op => op.value === this.operator).text
                }, doctor_operator_number: this.doctor_operator_number});
            }
        },
    }
}
</script>

<style>

</style>
