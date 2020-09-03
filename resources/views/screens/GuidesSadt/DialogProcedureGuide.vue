<template>
    <v-card>
        <v-card-title class="headline grey lighten-2">
            {{ action === 'store' ? 'Vincular' : 'Editar' }} procedimento
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
                    <v-col class="d-flex" cols="12">
                        <v-autocomplete
                            v-model="procedure"
                            :items="procedures_select"
                            dense
                            filled
                            label="Procedimento *"
                            :item-text="procedures_select => `${procedures_select.number} - R$ ${convertToPrice(procedures_select.procedure_operator.price)} - ${procedures_select.description}`"
                            :item-value="procedures_select => procedures_select"
                            required
                            @change="setUnityPrice"
                            :readonly="!!this.guide_procedure"
                        ></v-autocomplete>
                    </v-col>
                </v-row>
                <v-row v-if="procedure">
                    <v-col cols="12" md="2">
                        <v-text-field v-mask="onlyInteger" label="Qtde. Solic. *" required
                                      v-model="request_amount"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-text-field label="Data de execução *" required type="date"
                                      v-model="execution_date"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="2">
                        <v-text-field v-mask="onlyInteger" label="Qtde. Aut. *" required
                                      v-model="permission_amount"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-currency-field prefix="R$ " label="Preço Unitário *" required
                                          v-model="unity_price"></v-currency-field>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="$emit('action')">Fechar</v-btn>
            <v-btn color="blue darken-1" text @click="salvar">{{ action === 'store' ? 'Adicionar' : 'Salvar' }}</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    name: "dialog-procedure-guide",
    props: {
        guide_procedure: {
            type: Object,
        },
        procedures: {
            type: Array,
            default: () => ([])
        },
        procedures_guide: {
            type: Array,
            default: () => ([])
        }
    },
    data() {
        return {
            action: 'store',
            procedure: null,
            errors: [],
            execution_date: '',
            request_amount: '',
            permission_amount: '',
            unity_price: ''
        }
    },
    computed: {
        procedures_select() {
            if (this.guide_procedure) {
                return this.procedures.filter(pr => pr.id === this.guide_procedure.guide_procedure.procedure_id);
            } else if (this.procedures_guide.length > 0) {
                return this.procedures.filter(pr => !this.procedures_guide.find(pr_guide => pr.id === pr_guide.guide_procedure.procedure_id));
            } else {
                return this.procedures;
            }
        },
    },
    created() {
        this.verify();
    },
    methods: {
        setUnityPrice(data) {
            this.unity_price = data.procedure_operator.price;
        },
        verify() {
            if (this.guide_procedure) {
                this.action = 'update';
                this.procedure = this.procedures.find(pr => pr.id === this.guide_procedure.guide_procedure.procedure_id);
                this.execution_date = this.guide_procedure.guide_procedure.execution_date;
                this.request_amount = this.guide_procedure.guide_procedure.request_amount;
                this.permission_amount = this.guide_procedure.guide_procedure.permission_amount;
                this.unity_price = this.guide_procedure.guide_procedure.unity_price;
            }
        },
        salvar() {
            this.errors = []
            if (!this.procedure || !this.execution_date || !this.request_amount || !this.permission_amount || !this.unity_price) {
                this.errors.push('Preencha todos os campos');
            } else if (Number(this.permission_amount) > Number(this.request_amount)) {
                this.errors.push('A quantidade permitida/executada deve ser menor ou igual a quantidade solicitada!');
            } else {
                this.$emit('action', {
                    action: this.action,
                    procedure: this.procedure,
                    execution_date: this.execution_date,
                    request_amount: this.request_amount,
                    permission_amount: this.permission_amount,
                    unity_price: this.unity_price
                });
                this.execution_date = '';
                this.request_amount = '';
                this.permission_amount = '';
                this.unity_price = '';
                this.procedure = null;
            }
        }
    }
}
</script>

<style scoped>

</style>
