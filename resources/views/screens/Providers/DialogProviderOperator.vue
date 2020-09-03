<template>
    <div class="text-center">
        <v-card>
            <v-card-title class="headline grey lighten-2">
                Vincular prestador a operadora
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
                                :readonly="!!provider_operator"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Cód. na operadora *" required
                                          v-model="provider_operator_number"></v-text-field>
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
    name: "dialog-provider-operator",
    props: {
        provider_operator: {
            type: Object,
        },
        operators: {
            type: Array,
            default: () => ([])
        },
        operators_provider: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            action: 'store',
            errors: [],
            operator: '',
            provider_operator_number: ''
        }
    },
    created() {
        this.verify();
    },
    computed: {
        operators_select() {
            if (this.provider_operator) {
                return this.operators.filter(op => op.id === this.provider_operator.provider_operator.operator_id);
            } else if (!!this.operators_provider) {
                return this.operators.filter(op => !this.operators_provider.find(op_pv => op.id === op_pv.provider_operator.operator_id));
            } else {
                return this.operators;
            }
        }
    },
    methods: {
        verify() {
            if (this.provider_operator) {
                this.action = 'update';
                this.operator = {
                    id: this.provider_operator.id,
                    name: this.provider_operator.name,
                    ans: this.provider_operator.ans
                };
                this.provider_operator_number = this.provider_operator.provider_operator.provider_operator_number;
            }
        },
        salvar() {
            this.errors = []
            if (!this.operator || !this.provider_operator_number) {
                this.errors.push('Preencha todos os campos!');
            } else {
                this.$emit('action', {
                    action: this.action,
                    operator: this.operator,
                    provider_operator_number: this.provider_operator_number
                });
            }
        },
    }
}
</script>

<style scoped>

</style>
