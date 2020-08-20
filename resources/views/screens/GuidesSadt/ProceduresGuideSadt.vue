<template>
    <v-container fluid>
        <v-row>
            <v-col class="d-flex" cols="12" md="6">
                <v-autocomplete
                    v-model="procedure"
                    :items="procedures"
                    filled
                    dense
                    label="Procedimento *"
                    :item-text="procedures => `${procedures.number} - ${procedures.description}`"
                    :item-value="procedures => procedures"
                    required
                ></v-autocomplete>
            </v-col>
            <v-col cols="12" md="2" v-if="procedures.length > 0 && procedure">
                <v-text-field type="number" label="27 - Qtde. Solic" placeholder="Qtde. Solic."
                              v-model="request_amount"></v-text-field>
            </v-col>
            <v-col cols="12" md="2" v-if="procedures.length > 0 && procedure">
                <v-text-field type="number" label="27 - Qtde. Aut." placeholder="Qtde. Aut"
                              v-model="permission_amount"></v-text-field>
            </v-col>
            <v-col cols="12" md="2" v-if="procedures.length > 0 && procedure">
                <v-btn color="primary" @click="addProcedure">Incluir</v-btn>
            </v-col>
        </v-row>
        <v-simple-table>
            <template v-slot:default>
                <thead>
                <tr>
                    <th class="text-left">Cód. do Procedimento</th>
                    <th class="text-left">Descrição</th>
                    <th class="text-left">Qtde. Solic.</th>
                    <th class="text-left">Qtde. Aut.</th>
                    <th class="text-left">Excluir</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in guide_procedures" :key="item.guide_procedure.procedure_id">
                    <td>{{ item.number }}</td>
                    <td>{{ item.description.substring(60, 0) }}</td>
                    <td>{{ item.guide_procedure.request_amount }}</td>
                    <td>{{ item.guide_procedure.permission_amount }}</td>
                    <td>
                        <v-icon @click="remove(item)">mdi-delete</v-icon>
                    </td>
                </tr>
                </tbody>
            </template>
        </v-simple-table>
    </v-container>
</template>

<script>
export default {
    name: "procedures-guide-sadt",
    props: {
        guide: {
            type: Object
        },
    },
    data() {
        return {
            procedures: [],
            procedure: null,
            request_amount: '',
            permission_amount: '',
            execution_date: '',
            unity_price: '',
            total_price: '',
            guide_procedures: []
        }
    },
    created() {
        this.getProcedures();
        this.verify();
    },
    methods: {
        verify() {
            if (this.guide && this.guide.procedures.length > 0) {
                this.guide_procedures = this.guide.procedures;
            }
        },
        getProcedures() {
            this.request().get('/procedures').then(response => {
                this.procedures = response.data.data;
            }).catch(err => {
                console.log(err);
                this.procedures = [];
                this.procedure = null;
            })
        },
        addProcedure() {
            if (this.procedure && this.request_amount && this.permission_amount && Number(this.permission_amount) <= Number(this.request_amount)) {
                this.guide_procedures.push({
                    number: this.procedure.number,
                    description: this.procedure.description,
                    guide_procedure: {
                        procedure_id: this.procedure.id,
                        request_amount: this.request_amount,
                        permission_amount: this.permission_amount
                    }
                });
                this.procedure = null;
                this.request_amount = '';
                this.permission_amount = '';
            }
        },
        remove(data) {
            this.guide_procedures.splice(this.guide_procedures.findIndex(guide_pc => guide_pc.guide_procedure.procedure_id === data.guide_procedure.procedure_id), 1);
        }
    }
}
</script>

<style scoped>

</style>
