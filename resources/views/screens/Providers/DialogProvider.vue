<template>
    <div class="text-center">
        <v-card>
            <v-card-title class="headline grey lighten-2">
                {{ action === 'store' ? 'Novo prestador' : 'Editar prestador' }}
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
                        <v-col cols="12">
                            <v-text-field label="Código CNES" hint="Usado nas guias SADT" persistent-hint
                                          v-model="cnes"></v-text-field>
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
                    <tr v-for="(item, idx) in provider_operators" :key="idx">
                        <td>{{ item.name }}</td>
                        <td>{{ item.provider_operator.provider_operator_number }}</td>
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
                <dialog-provider-operator v-if="dialog" :operators="operators" :operators_provider="provider_operators"
                                          :provider_operator="provider_operator"
                                          @action="save"></dialog-provider-operator>
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
import DialogProviderOperator from "./DialogProviderOperator";

export default {
    name: 'dialog-provider',
    components: {
        DialogProviderOperator
    },
    props: {
        provider: {
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
        provider_operators() {
            return this.provider.operators;
        }
    },
    data() {
        return {
            dialog: false,
            action: 'store',
            errors: [],
            name: '',
            cnes: '',
            provider_operator: null,
        }
    },
    created() {
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if (this.provider.id) {
                this.name = this.provider.name;
                this.cnes = this.provider.cnes;
                this.action = 'update';
            }
        },
        salvar() {
            this.toggleLoading();
            this.errors = [];
            if (this.action === 'store') {
                this.request().post('/providers/store', {
                    name: this.name,
                    cnes: this.cnes,
                    operators: this.provider_operators
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
                this.request().put(`/providers/${this.provider.id}/update`, {
                    name: this.name,
                    cnes: this.cnes,
                    operators: this.provider_operators
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
                    this.provider.operators.push({
                        ...data.operator,
                        provider_operator: {
                            provider_id: this.provider.id,
                            operator_id: data.operator.id,
                            provider_operator_number: data.provider_operator_number,
                        }
                    });
                } else {
                    this.provider.operators.map(op => {
                        if (op.provider_operator.operator_id === data.operator.id) {
                            op.provider_operator.provider_operator_number = data.provider_operator_number;
                        }
                        return op;
                    })
                }
            }
            this.provider_operator = null;
            this.dialog = false;
        },
        open(data) {
            this.provider_operator = data;
            this.dialog = true;
        },
        remove(index) {
            this.provider.operators.splice(index, 1);
        }
    }
}
</script>

<style>

</style>
