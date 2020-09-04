<template>
    <div>
        <v-dialog
            v-model="dialog"
        >
            <dialog-patient v-if="dialog" :operators="operators" :patient="patient" @action="close"></dialog-patient>
        </v-dialog>
        <data-table
            v-if="!!filters.operator"
            :columns="columns"
            :url="url"
            :filters="filters"
            @loading="toggleLoading"
            @finished-loading="toggleLoading"
            order-dir="desc"
            ref="table">
            <div slot="filters" slot-scope="{ tableData, perPage }">
                <div class="row mb-2 justify-content-between align-items-center">
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
                            placeholder="Pesquisar pelos campos da tabela">
                    </div>
                    <div class="col-md-2">
                        <select
                            v-model="tableData.filters.operator"
                            class="form-control">
                            <option v-for="operator in operators" :key="operator.id" :value="operator.id">
                                {{ operator.name }} - {{ operator.ans }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2 text-right">
                        <v-btn class="ma-2 text-decoration-none" small color="info" href="/dashboard/api/export/patients">
                            <v-icon left>mdi-pdf-box</v-icon>
                            Exportar
                        </v-btn>
                    </div>
                    <div class="col-md-2 text-right">
                        <v-btn class="ma-2" small color="success" @click="open(null)">
                            <v-icon left>mdi-plus</v-icon>
                            Novo
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
            filters: {
                operator: ''
            },
            dialog: false,
            patient: {},
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    columnName: 'patients.id',
                    orderable: true
                },
                {
                    label: 'Nome',
                    name: 'name',
                    columnName: 'patients.name',
                    orderable: true
                },
                {
                    label: 'Nº CNS',
                    name: 'cns',
                    columnName: 'patients.cns',
                    orderable: false
                },
                {
                    label: 'Carteiras - Validade',
                    name: 'operators',
                    meta: {
                        name: 'patient_operator',
                    },
                    component: IndexPatientOperator,
                    orderable: false
                },
                {
                    label: '',
                    name: 'Editar',
                    classes: {
                        color: 'primary',
                        icon: 'mdi-pencil'
                    },
                    width: 5,
                    orderable: false,
                    event: "click",
                    handler: this.open,
                    component: ActionsTable
                },
                {
                    label: '',
                    name: 'Excluir',
                    classes: {
                        color: 'error',
                        icon: 'mdi-delete'
                    },
                    width: 5,
                    orderable: false,
                    event: "click",
                    handler: this.delete,
                    component: ActionsTable
                },
            ]
        }
    },
    created() {
        this.getOperators();
    },
    methods: {
        getOperators() {
            this.request().get('/operators/select').then(response => {
                this.operators = response.data.data;
                if (!!this.operators)
                    this.filters.operator = this.operators[0].id;
            }).catch(err => {
                this.operators = [];
            });
        },
        open(data) {
            this.toggleLoading();
            this.patient = data || {
                operators: []
            };
            if (!!data) {
                this.request().get(`/patients/${data.id}`).then(response => {
                    this.patient.operators = response.data.data;
                }).catch(err => {
                    this.patient.operators = [];
                }).finally(() => {
                    this.toggleLoading();
                    this.dialog = true;
                });
            } else {
                this.toggleLoading();
                this.dialog = true;
            }
        },
        close() {
            this.dialog = false;
            this.$refs.table.getData();
        },
        delete(data) {
            if (window.confirm("Tem certeza que deseja excluir o paciente?\nIsso pode causar inconsistências no sistema pois ele pode estar vinculado a uma guia")) {
                this.toggleLoading();
                this.request().delete(`/patients/${data.id}/delete`).then(() => {
                    this.$refs.table.getData();
                }).finally(() => {
                    this.toggleLoading();
                });
            }
        }
    },
}
</script>

<style>

</style>
