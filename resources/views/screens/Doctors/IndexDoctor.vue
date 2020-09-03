<template>
    <div>
        <v-dialog
            v-model="dialog"
            width="800"
        >
            <dialog-doctor v-if="dialog" :operators="operators" :doctor="doctor" @action="close"></dialog-doctor>
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
                    <div class="col-md-4">
                        <select
                            v-model="tableData.filters.operator"
                            class="form-control">
                            <option v-for="operator in operators" :key="operator.id" :value="operator.id">
                                {{ operator.name }} - {{ operator.ans }}
                            </option>
                        </select>
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
import DialogDoctor from './DialogDoctor'
import IndexDoctorOperator from './IndexDoctorOperator'

export default {
    name: 'IndexDoctor',
    components: {
        DialogDoctor,
        IndexDoctorOperator
    },
    data() {
        return {
            url: "/dashboard/api/doctors",
            operators: [],
            filters: {
                operator: ''
            },
            dialog: false,
            doctor: {},
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    columnName: 'doctors.id',
                    orderable: true
                },
                {
                    label: 'Nome',
                    name: 'name',
                    columnName: 'doctors.name',
                    orderable: true
                },
                {
                    label: 'Conselho',
                    name: 'cp_formatted',
                    columnName: 'cp',
                    orderable: false
                },
                {
                    label: 'Nº no Conselho',
                    name: 'advice_number',
                    columnName: 'doctors.advice_number',
                    orderable: false
                },
                {
                    label: 'UF',
                    name: 'uf_formatted',
                    columnName: 'uf',
                    orderable: false
                },
                {
                    label: 'CBO',
                    name: 'cbo',
                    orderable: false
                },
                {
                    label: 'Cód. na Operadora',
                    name: 'operators',
                    columnName: 'doctor_operators.doctor_operator_number',
                    meta: {
                        column: 'doctor_operator_number'
                    },
                    component: IndexDoctorOperator,
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
            })
        },
        open(data) {
            this.toggleLoading();
            this.doctor = data || {
                operators: []
            };
            if (!!data) {
                this.request().get(`/doctors/${data.id}`).then(response => {
                    this.doctor.operators = response.data.data;
                }).catch(err => {
                    this.doctor.operators = [];
                }).finally(() => {
                    this.toggleLoading();
                    this.dialog = true;
                });
            } else {
                this.toggleLoading();
                this.dialog = true;
            }
        },
        close(data) {
            this.dialog = false;
            this.$refs.table.getData();
        },
        delete(data) {
            if (window.confirm("Tem certeza que deseja excluir o médico?\nIsso pode causar inconsistências no sistema pois ele pode estar vinculado a uma guia")) {
                this.toggleLoading();
                this.request().delete(`/doctors/${data.id}/delete`).then(response => {
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
