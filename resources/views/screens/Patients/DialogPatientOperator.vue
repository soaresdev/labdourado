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
                    <v-col class="d-flex" cols="12" md="4">
                        <v-autocomplete
                            v-model="operator"
                            :items="operators_select"
                            dense
                            filled
                            label="Operadora *"
                            :item-text="operators => `${operators.name} - ${operators.ans}`"
                            :item-value="operators => operators"
                            :readonly="!!patient_operator"
                        ></v-autocomplete>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field label="Número da carteira *" required v-model="wallet_number"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field label="Validade" type="date" v-model="wallet_expiration"></v-text-field>
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
    name: 'dialog-patient-operator',
    props: {
        patient_operator: {
            type: Object
        },
        operators: {
            type: Array,
            default: () => ([])
        },
        operators_patient: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            action: 'store',
            errors: [],
            operator: '',
            wallet_number: '',
            wallet_expiration: '',
        }
    },
    computed: {
        operators_select() {
            if (this.patient_operator) {
                return this.operators.filter(op => op.id === this.patient_operator.patient_operator.operator_id);
            } else if (!!this.operators_patient) {
                return this.operators.filter(op => !this.operators_patient.find(op_pat => op.id === op_pat.patient_operator.operator_id));
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
            if (this.patient_operator) {
                this.action = 'update';
                this.operator = {
                    id: this.patient_operator.id,
                    name: this.patient_operator.name,
                    ans: this.patient_operator.ans
                };
                this.wallet_number = this.patient_operator.patient_operator.wallet_number;
                this.wallet_expiration = this.patient_operator.patient_operator.wallet_expiration;
            }
        },
        salvar() {
            this.errors = []
            if (!this.operator || !this.wallet_number) {
                this.errors.push('Preencha a operadora e o número da carteira todos os campos!')
            } else {
                this.$emit('action', {
                    action: this.action,
                    operator: this.operator,
                    wallet_number: this.wallet_number,
                    wallet_expiration: this.wallet_expiration
                });
            }
        }
    }
}
</script>

<style>

</style>
