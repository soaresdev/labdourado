<template>
    <div class="text-center">
        <v-card>
            <v-card-title class="headline grey lighten-2">
                {{ action === 'store' ? 'Novo procedimento' : 'Editar procedimento' }}
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
                        <v-col cols="12" md="6">
                            <v-text-field label="Cód. do Procedimento" required v-model="number"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Descrição *" required v-model="description"></v-text-field>
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
                        <th class="text-left">Preço (R$)</th>
                        <th class="text-left">Editar</th>
                        <th class="text-left">Excluir</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, idx) in procedure_operators" :key="idx">
                        <td>{{ item.name }}</td>
                        <td>{{ convertToString(item.procedure_operator.price) }}</td>
                        <td>
                            <v-icon @click="open(item)">mdi-pencil</v-icon>
                        </td>
                        <td>
                            <v-icon @click="remove(idx)">mdi-delete</v-icon>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
            <v-dialog
                v-model="dialog"
                width="800"
            >
                <dialog-procedure-operator v-if="dialog" :operators="operators" :operators_procedure="procedure_operators"
                                         :procedure_operator="procedure_operator"
                                         @action="save"></dialog-procedure-operator>
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
import DialogProcedureOperator from './DialogProcedureOperator'
export default {
number: "dialog-procedure",
    components: {
        DialogProcedureOperator
    },
    props: {
        procedure: {
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
            action: 'store',
            errors: [],
            number: '',
            description: '',
            operator: '',
            procedure_operator: null
        }
    },
    computed: {
        procedure_operators() {
            return this.procedure.operators;
        }
    },
    created() {
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if (this.procedure.id) {
                this.number = this.procedure.number;
                this.description = this.procedure.description;
                this.action = 'update';
            }
        },
        salvar() {
            this.toggleLoading();
            this.errors = [];
            if (this.action === 'store') {
                this.request().post('/procedures/store', {
                    number: this.number,
                    description: this.description,
                    operators: this.procedure_operators,
                }).then(response => {
                    this.$emit('action')
                }).catch(err => {
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                }).finally(() => {
                    this.toggleLoading();
                });
            } else {
                this.request().put(`/procedures/${this.procedure.id}/update`, {
                    number: this.number,
                    description: this.description,
                    operators: this.procedure_operators,
                }).then(response => {
                    this.$emit('action')
                }).catch(err => {
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                }).finally(() => {
                    this.toggleLoading();
                });
            }
        },
        save(data) {
            if (data) {
                if (data.action === 'store') {
                    this.procedure.operators.push({
                        ...data.operator,
                        procedure_operator: {
                            procedure_id: this.procedure.id,
                            operator_id: data.operator.id,
                            price: data.price
                        }
                    });
                } else {
                    this.procedure.operators.map(op => {
                        if (op.procedure_operator.operator_id === data.operator.id) {
                            op.procedure_operator.price = data.price;
                        }
                        return op;
                    })
                }
            }
            this.procedure_operator = null;
            this.dialog = false;
        },
        open(data) {
            this.procedure_operator = data;
            this.dialog = true;
        },
        remove(index) {
            this.procedure.operators.splice(index, 1);
        }
    }
}
</script>

<style scoped>

</style>
