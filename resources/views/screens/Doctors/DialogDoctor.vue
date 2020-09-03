<template>
    <div class="text-center">
        <v-card>
            <v-card-title class="headline grey lighten-2">
                {{ action === 'store' ? 'Novo médico' : 'Editar médico' }}
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
                            <v-text-field label="Nome *" required v-model="name"></v-text-field>
                        </v-col>
                        <v-col class="d-flex" cols="12" md="2">
                            <v-select
                                v-model="cp"
                                :items="cps"
                                filled
                                label="Conselho Profissional *"
                                :item-text="cps.text"
                                :item-value="cps.value"
                            ></v-select>
                        </v-col>
                        <v-col class="d-flex" cols="12" md="2">
                            <v-select
                                v-model="uf"
                                :items="ufs"
                                filled
                                label="UF *"
                                :item-text="ufs.text"
                                :item-value="ufs.value"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field label="Código CBO *" required v-model="cbo"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="4">
                            <v-text-field label="Número no conselho *" required v-model="advice_number"></v-text-field>
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
                        <th class="text-left">Cód. na operadora</th>
                        <th class="text-left">Editar</th>
                        <th class="text-left">Excluir</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item, idx) in doctor_operators" :key="idx">
                        <td>{{ item.name }}</td>
                        <td>{{ item.doctor_operator.doctor_operator_number }}</td>
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
                <dialog-doctor-operator v-if="dialog" :operators="operators" :operators_doctor="doctor_operators"
                                        :doctor_operator="doctor_operator" @action="save"></dialog-doctor-operator>
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
import {cps, ufs} from './selects'
import DialogDoctorOperator from './DialogDoctorOperator'

export default {
    name: 'dialog-doctor',
    components: {
        DialogDoctorOperator
    },
    props: {
        doctor: {
            type: Object,
            default: () => ({
                operators: []
            })
        },
        operators: {
            type: Array,
            default: () => ([])
        }
    },
    computed: {
        doctor_operators() {
            return this.doctor.operators;
        }
    },
    data() {
        return {
            dialog: false,
            action: 'store',
            errors: [],
            ufs,
            cps,
            name: '',
            cp: '06',
            uf: '31',
            advice_number: '',
            cbo: '',
            doctor_operator: null,
        }
    },
    created() {
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if (this.doctor.id) {
                this.name = this.doctor.name;
                this.cp = this.doctor.cp;
                this.uf = this.doctor.uf;
                this.advice_number = this.doctor.advice_number;
                this.cbo = this.doctor.cbo;
                this.action = 'update';
            }
        },
        salvar() {
            this.toggleLoading();
            this.errors = [];
            if (this.action === 'store') {
                this.request().post('/doctors/store', {
                    name: this.name,
                    cp: this.cp,
                    uf: this.uf,
                    advice_number: this.advice_number,
                    cbo: this.cbo,
                    operators: this.doctor_operators
                }).then(response => {
                    this.$emit('action', response.data.data);
                }).catch(err => {
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                }).finally(() => {
                    this.toggleLoading();
                });
            } else {
                this.request().put(`/doctors/${this.doctor.id}/update`, {
                    name: this.name,
                    cp: this.cp,
                    uf: this.uf,
                    advice_number: this.advice_number,
                    cbo: this.cbo,
                    operators: this.doctor_operators
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
                    this.doctor.operators.push({
                        ...data.operator,
                        doctor_operator: {
                            operator_id: data.operator.id,
                            doctor_id: this.doctor.id,
                            doctor_operator_number: data.doctor_operator_number,
                        }
                    })
                } else {
                    this.doctor.operators.map(op => {
                        if (op.doctor_operator.operator_id === data.operator.id) {
                            op.doctor_operator.doctor_operator_number = data.doctor_operator_number;
                        }
                        return op;
                    })
                }
            }
            this.doctor_operator = null;
            this.dialog = false;
        },
        open(data) {
            this.doctor_operator = data;
            this.dialog = true;
        },
        remove(index) {
            this.doctor.operators.splice(index, 1);
        }
    }
}
</script>

<style>

</style>
