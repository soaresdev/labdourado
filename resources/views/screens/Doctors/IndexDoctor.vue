<template>
    <div>
        <v-dialog
            v-model="dialog"
            width="800"
        >
            <dialog-doctor v-if="dialog" :operators="operators" :doctor="doctor" @action="close"></dialog-doctor>
        </v-dialog>
        <data-table
            :columns="columns"
            :url="url"
            :filters="filters"
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
                            placeholder="Pesquisar">
                    </div>
                    <div class="col-md-4">
                        <select
                            v-model="tableData.filters.operator"
                            class="form-control">
                            <option v-for="operator in operators" :key="operator.value" :value="operator.value">
                                {{ operator.text }}
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
                    orderable: true,
                },
                {
                    label: 'Nome',
                    name: 'name',
                    columnName: 'doctors.name',
                    orderable: true,
                },
                {
                    label: 'Conselho',
                    name: 'cp_formatted',
                    columnName: 'cp',
                    orderable: false,
                    searchable: false
                },
                {
                    label: 'Nº no Conselho',
                    name: 'advice_number',
                    columnName: 'doctors.advice_number',
                    orderable: false,
                },
                {
                    label: 'UF',
                    name: 'uf_formatted',
                    columnName: 'uf',
                    orderable: false,
                    searchable: false
                },
                {
                    label: 'CBO',
                    name: 'cbo',
                    orderable: false,
                },
                {
                    label: 'Cód. na Operadora',
                    name: 'operators',
                    meta: {
                        column: 'doctor_operator_number'
                    },
                    component: IndexDoctorOperator,
                    orderable: false,
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
                    component: ActionsTable,
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
                    component: ActionsTable,
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
            this.doctor = data || {
                operators: []
            };
            this.dialog = true;
        },
        close(data) {
            this.dialog = false;
            this.$refs.table.getData();
        },
        delete(data) {
            if (window.confirm("Tem certeza que deseja excluir o médico?\nIsso pode causar inconsistências no sistema pois ele pode estar vinculado a uma guia")) {
                this.request().delete(`/doctors/${data.id}/delete`).then(response => {
                    this.$refs.table.getData();
                });
            }
        }
    },
}
</script>

<style>

</style>
