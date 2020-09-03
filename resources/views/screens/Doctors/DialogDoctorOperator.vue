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
                        <v-col class="d-flex" cols="12" md="6">
                            <v-autocomplete
                                v-model="operator"
                                :items="operators_select"
                                dense
                                filled
                                label="Operadora *"
                                :item-text="operators => `${operators.name} - ${operators.ans}`"
                                :item-value="operators => operators"
                                :readonly="!!doctor_operator"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field label="Código na operadora *" required
                                          v-model="doctor_operator_number"></v-text-field>
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
            action: 'store',
            errors: [],
            operator: '',
            doctor_operator_number: ''
        }
    },
    created() {
        this.verify();
    },
    computed: {
        operators_select() {
            if (this.doctor_operator) {
                return this.operators.filter(op => op.id === this.doctor_operator.doctor_operator.operator_id)
            } else if (!!this.operators_doctor) {
                return this.operators.filter(op => !this.operators_doctor.find(op_doc => op.id === op_doc.doctor_operator.operator_id));
            } else {
                return this.operators;
            }
        }
    },
    methods: {
        verify() {
            if (this.doctor_operator) {
                this.action = 'update';
                this.operator = {
                    id: this.doctor_operator.id,
                    name: this.doctor_operator.name,
                    ans: this.doctor_operator.ans
                };
                this.doctor_operator_number = this.doctor_operator.doctor_operator.doctor_operator_number;
            }
        },
        salvar() {
            this.errors = []
            if (!this.operator || !this.doctor_operator_number) {
                this.errors.push('Preencha todos os campos!')
            } else {
                this.$emit('action', {
                    action: this.action,
                    operator: this.operator,
                    doctor_operator_number: this.doctor_operator_number
                });
            }
        },
    }
}
</script>

<style>

</style>
