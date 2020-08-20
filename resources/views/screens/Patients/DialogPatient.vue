<template>
    <div class="text-center">
        <v-card>
            <v-card-title class="headline grey lighten-2">
                {{ action === 'store' ? 'Novo paciente' : 'Editar paciente' }}
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
                            <v-text-field label="Nome *" required v-model="name"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Carteira nacional de saúde" v-model="cns"></v-text-field>
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
                        <th class="text-left">Nº Carteira</th>
                        <th class="text-left">Validade</th>
                        <th class="text-left">Editar</th>
                        <th class="text-left">Excluir</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in patient_operators" :key="item.wallet_number">
                        <td>{{ item.operator }}</td>
                        <td>{{ item.wallet_number }}</td>
                        <td>{{ item.wallet_expiration ? format(item.wallet_expiration) : '' }}</td>
                        <td>
                            <v-icon @click="open(item)">mdi-pencil</v-icon>
                        </td>
                        <td>
                            <v-icon @click="remove(item)">mdi-delete</v-icon>
                        </td>
                    </tr>
                    </tbody>
                </template>
            </v-simple-table>
            <v-dialog
                v-model="dialog"
                width="800"
            >
                <dialog-patient-operator v-if="dialog" :operators="operators" :operators_patient="patient_operators"
                                         :patient_operator="patient_operator" @action="save"></dialog-patient-operator>
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
import DialogPatientOperator from './DialogPatientOperator'

export default {
    name: 'dialog-patient',
    components: {
        DialogPatientOperator
    },
    props: {
        patient: {
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
            name: '',
            patient_operator: null,
            cns: '',
            action: 'store',
            errors: []
        }
    },
    computed: {
        patient_operators() {
            return this.patient.operators.map(pat_op => {
                pat_op.patient_operator.operator = pat_op.name;
                return pat_op.patient_operator;
            })
        }
    },
    created() {
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if (this.patient.id) {
                this.name = this.patient.name
                this.cns = this.patient.cns
                this.action = 'update'
            } else {
                this.action = 'store'
            }
        },
        salvar() {
            this.errors = [];
            if (this.action === 'store') {
                this.request().post('/patients/store', {
                    name: this.name,
                    operators: this.patient_operators,
                    cns: this.cns,
                }).then(response => {
                    this.$emit('action', {
                        id: response.data.data[0].id,
                        name: response.data.data[0].name,
                        operators: this.patient_operators,
                        patient_operator: this.patient_operators[0],
                        cns: response.data.data[0].cns
                    })
                }).catch(err => {
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            } else {
                this.request().put(`/patients/${this.patient.id}/update`, {
                    name: this.name,
                    operators: this.patient_operators,
                    cns: this.cns,
                }).then(response => {
                    this.$emit('action')
                }).catch(err => {
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            }
        },
        save(data) {
            if (data) {
                if (data.action === 'store') {
                    this.patient.operators.push({
                        name: data.operator.text,
                        patient_operator: {
                            operator_id: data.operator.value,
                            wallet_number: data.wallet_number,
                            wallet_expiration: data.wallet_expiration,
                            patient_id: this.patient.id
                        }
                    })
                } else {
                    this.patient.operators.map(op => {
                        if (op.patient_operator.operator_id === data.operator.value) {
                            op.patient_operator.wallet_number = data.wallet_number;
                            op.patient_operator.wallet_expiration = data.wallet_expiration;
                        }
                        return op;
                    })
                }
            }
            this.patient_operator = null;
            this.dialog = false;
        },
        open(data) {
            this.patient_operator = data;
            this.dialog = true;
        },
        remove(data) {
            this.patient.operators.splice(this.patient.operators.findIndex(pat_op => pat_op.patient_operator.wallet_number === data.wallet_number), 1);
        }
    }
}
</script>

<style>

</style>
