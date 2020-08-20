<template>
    <div class="text-center">
        <v-card>
            <v-card-title class="headline grey lighten-2">
                {{ action === 'store' ? 'Novo lote' : 'Editar lote' }}
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
                            <v-text-field label="Número do lote *" required v-model="number"></v-text-field>
                        </v-col>
                        <v-col class="d-flex" cols="12" md="6">
                            <v-select
                                v-model="operator"
                                :items="operators_select"
                                filled
                                label="Operadora *"
                                :item-text="operators_select.text"
                                :item-value="operators_select.value"
                                :readonly="!!lot_selected"
                            ></v-select>
                        </v-col>
                    </v-row>
                    <v-row v-if="this.lot">
                        <v-col cols="12" md="6">
                            <v-btn color="success" @click="toggle">
                                {{ lot_selected.closed_at ? 'Abrir faturamento' : 'Fechar faturamento' }}
                            </v-btn>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-btn :disabled="!lot_selected.closed_at" color="primary darken-1" @click="xml">Exportar
                                XML
                            </v-btn>
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
    name: 'dialog-lot',
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
            lot_selected: null,
            dialog: false,
            number: '',
            action: 'store',
            operator: '',
            errors: []
        }
    },
    computed: {
        operators_select() {
            if (this.lot) {
                return this.operators.filter(op => op.value === this.lot.operator.id);
            } else {
                return this.operators;
            }
        },
    },
    created() {
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if (this.lot) {
                this.lot_selected = this.lot;
                this.number = this.lot.number;
                this.operator = this.lot.operator.id;
                this.action = 'update'
            } else {
                this.action = 'store'
            }
        },
        salvar() {
            this.errors = [];
            if (this.action === 'store') {
                this.request().post('/lots/store', {
                    number: this.number,
                    operator: this.operator
                }).then(response => {
                    this.$emit('action')
                }).catch(err => {
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            } else {
                this.request().put(`/lots/${this.lot.id}/update`, {
                    number: this.number
                }).then(response => {
                    this.$emit('action')
                }).catch(err => {
                    if (err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            }
        },
        toggle() {
            let msg = this.lot_selected.closed_at ? window.confirm("Tem certeza que deseja reabrir o lote?") : window.confirm("Tem certeza que deseja fechar o lote?");
            if (msg) {
                this.request().put(`/lots/${this.lot.id}/toggle`).then(response => {
                    this.lot_selected = response.data.data[0];
                }).catch(err => {
                    console.log(err);
                })
            }
        },
        xml() {
            this.request().put(`/lots/${this.lot.id}/xml`).then(response => {
                let element = document.createElement('a');
                element.setAttribute('href', response.data.data.url);
                element.setAttribute('download', response.data.data.url.split('/')[response.data.data.url.split('/').length - 1]);

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);
                this.$emit('action');
            }).catch(err => {
                console.log(err);
            })
        }
    }
}
</script>

<style>

</style>
