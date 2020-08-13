<template>
    <div>
        <v-dialog
      v-model="dialog"
    >
        <dialog-patient v-if="dialog" :operators="operators" :patient="patient" @action="close"></dialog-patient>
    </v-dialog>
    <data-table
        :columns="columns"
        :url="url"
        :filters="filters"
        ref="table">
        <div slot="filters" slot-scope="{ tableData, perPage }">
        <div class="row mb-2 justify-content-between">
            <div class="col-md-2">
                <select class="form-control" v-model="tableData.length">
                    <option :key="page" v-for="page in perPage">{{ page }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <input
                    name="name"
                    class="form-control"
                    v-model="tableData.search"
                    placeholder="Pesquisar">
            </div>
            <div class="col-md-4">
                <select
                    v-model="tableData.filters.operator"
                    class="form-control">
                    <option v-for="operator in operators" :key="operator.value" :value="operator.value">{{ operator.text }}</option>
                </select>
            </div>
            <div class="col-md-2 text-right">
                <v-btn class="ma-2" small color="success"  @click="open(null)">
                    <v-icon left>mdi-plus</v-icon> Novo
                </v-btn>
            </div>
        </div>
    </div>
    </data-table>
    </div>

</template>

<script>
import ActionsTable from '../../assets/js/components/ActionsTable'
import DialogPatient from './DialogPatient'
import IndexPatientOperator from './IndexPatientOperator'
export default {
    name: 'IndexPatient',
    components: {
        DialogPatient,
        IndexPatientOperator
    },
    data() {
        return {
            url: "/dashboard/api/patients",
            operators: [],
            dialog: false,
            patient: {},
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    columnName: 'patients.id',
                    orderable: true,
                },
                {
                    label: 'Nome',
                    name: 'name',
                    columnName: 'patients.name',
                    orderable: true,
                },
                {
                    label: 'Nº CNS',
                    name: 'cns',
                    columnName: 'patients.cns',
                },
                {
                    label: 'Nº Carteira',
                    name: 'wallet_number',
                    component: IndexPatientOperator,
                    orderable: false,
                },
                {
                    label: 'Validade',
                    name: 'wallet_expiration_formatted',
                    component: IndexPatientOperator,
                    orderable: false,
                },
                {
                label: '',
                name: 'Editar',
                classes: {
                    color: 'primary',
                    icon: 'mdi-pencil'
                },
                width: 10,
                orderable: false,
                event: "click",
                handler: this.open,
                component: ActionsTable,
            },
            {
                label: '',
                name: 'Excluir',
                classes: {
                    color: 'error',
                    icon: 'mdi-delete'
                },
                width: 10,
                orderable: false,
                event: "click",
                handler: this.delete,
                component: ActionsTable,
            },
            ],
            filters: {
                operator: ''
            },
        }
    },
    created(){
        this.getOperators();
    },
    methods: {
        getOperators(){
            this.request().get('/operators/select').then(response => {
                this.operators = response.data.data.map(op => {
                    return {
                        value: op.id,
                        text: op.name
                    };
                });
                this.$refs.table.filters.operator = this.operators[0].value;
            }).catch(err => {
                this.operators = [];
            })
        },
        open(data) {
            this.patient = data || {
                operators: []
            };
            this.dialog = true;
        },
        close() {
            this.dialog = false;
            this.$refs.table.getData();
        },
        delete(data) {
            this.request().delete(`/patients/${data.id}/delete`).then(response => {
                    this.$refs.table.getData();
                });
        }
    },
}
</script>

<style>

</style>
