<template>
    <v-card>
        <v-card-title class="headline grey lighten-2">
            Vincular procedimento a operadora
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
                    <v-col class="d-flex" cols="12" md="6">
                        <v-autocomplete
                            v-model="operator"
                            :items="operators_select"
                            dense
                            filled
                            label="Operadora *"
                            :item-text="operators => `${operators.name} - ${operators.ans}`"
                            :item-value="operators => operators"
                            :readonly="!!procedure_operator"
                        ></v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-currency-field prefix="R$ " label="Preço Unitário *" required
                                          v-model="price"></v-currency-field>
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
    name: "dialog-procedure-operator",
    props: {
        procedure_operator: {
            type: Object
        },
        operators: {
            type: Array,
            default: () => ([])
        },
        operators_procedure: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            action: 'store',
            operator: '',
            errors: [],
            price: ''
        }
    },
    computed: {
        operators_select() {
            if (this.procedure_operator) {
                return this.operators.filter(op => op.id === this.procedure_operator.procedure_operator.operator_id)
            } else if (!!this.operators_procedure) {
                return this.operators.filter(op => !this.operators_procedure.find(op_pr => op.id === op_pr.procedure_operator.operator_id));
            } else {
                return this.operators;
            }
        }
    },
    created() {
        this.verify();
    },
    methods: {
        verify() {
            if (this.procedure_operator) {
                this.action = 'update';
                this.operator = {
                    id: this.procedure_operator.id,
                    name: this.procedure_operator.name,
                    ans: this.procedure_operator.ans
                };
                this.price = this.convertToPrice(this.procedure_operator.procedure_operator.price);
            }
        },
        salvar() {
            this.errors = []
            if (!this.operator || !this.price) {
                this.errors.push('Preencha todos os campos!');
            } else {
                this.$emit('action', {
                    action: this.action,
                    operator: this.operator,
                    price: this.price
                });
            }
        }
    }
}
</script>

<style scoped>

</style>
