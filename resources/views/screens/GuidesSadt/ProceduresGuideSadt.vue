<template>
    <v-container fluid>
        <v-row align="center" justify="center">
            <v-btn color="primary" @click="dialog = true">Vincular procedimentos</v-btn>
        </v-row>
        <v-simple-table>
            <template v-slot:default>
                <thead>
                <tr>
                    <th class="text-left">Cód.</th>
                    <th class="text-left">Preço Un.</th>
                    <th class="text-left">Qtd. Solic.</th>
                    <th class="text-left">Qtd. Aut.</th>
                    <th class="text-left">Total</th>
                    <th class="text-left">Editar</th>
                    <th class="text-left">Excluir</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in guide_procedures" :key="item.guide_procedure.procedure_id">
                    <td>{{ item.number }}</td>
                    <td>{{ convertToString(item.guide_procedure.unity_price) }}</td>
                    <td>{{ item.guide_procedure.request_amount }}</td>
                    <td>{{ item.guide_procedure.permission_amount }}</td>
                    <td>{{
                            convertToString(item.guide_procedure.permission_amount * item.guide_procedure.unity_price)
                        }}
                    </td>
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
            <dialog-procedure-guide v-if="dialog" :procedures="procedures" :procedures_guide="guide_procedures"
                                    :guide_procedure="guide_procedure" @action="save"></dialog-procedure-guide>
        </v-dialog>
    </v-container>
</template>

<script>
import DialogProcedureGuide from "./DialogProcedureGuide"

export default {
    name: "procedures-guide-sadt",
    props: {
        guide: {
            type: Object
        },
        procedures: {
            type: Array,
            default: () => ([])
        }
    },
    components: {
        DialogProcedureGuide
    },
    data() {
        return {
            dialog: false,
            procedure: null,
            request_amount: '',
            permission_amount: '',
            execution_date: '',
            unity_price: '',
            guide_procedure: null
        }
    },
    computed: {
        guide_procedures() {
            this.$parent.$refs.treatment.total = this.guide.procedures.reduce((accumulator, procedure) => accumulator + (procedure.guide_procedure.permission_amount * procedure.guide_procedure.unity_price), 0);
            return this.guide.procedures;
        }
    },
    methods: {
        save(data) {
            if (data) {
                if (data.action === 'store') {
                    this.guide.procedures.push({
                        ...data.procedure,
                        guide_procedure: {
                            guide_id: this.guide.id,
                            procedure_id: data.procedure.id,
                            execution_date: data.execution_date,
                            request_amount: data.request_amount,
                            permission_amount: data.permission_amount,
                            unity_price: data.unity_price
                        }
                    })
                } else {
                    this.guide.procedures.map(pr => {
                        if (pr.guide_procedure.procedure_id === data.procedure.id) {
                            pr.guide_procedure.execution_date = data.execution_date;
                            pr.guide_procedure.request_amount = data.request_amount;
                            pr.guide_procedure.permission_amount = data.permission_amount;
                            pr.guide_procedure.unity_price = data.unity_price;

                        }
                        return pr;
                    })
                    this.dialog = false;
                }
            } else {
                this.dialog = false;
            }
            this.guide_procedure = null;
        },
        open(data) {
            this.guide_procedure = data;
            this.dialog = true;
        },
        remove(data) {
            this.guide_procedures.splice(this.guide_procedures.findIndex(guide_pc => guide_pc.guide_procedure.procedure_id === data.guide_procedure.procedure_id), 1);
        }
    }
}
</script>

<style scoped>

</style>
