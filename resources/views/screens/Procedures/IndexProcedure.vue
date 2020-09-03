<template>
    <div>
        <v-dialog
            v-model="dialog"
        >
            <dialog-procedure v-if="dialog" :operators="operators" :procedure="procedure"
                              @action="close"></dialog-procedure>
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
import DialogProcedure from './DialogProcedure'
import IndexProcedureOperator from './IndexProcedureOperator'

export default {
    name: 'IndexProcedure',
    components: {
        DialogProcedure,
        IndexProcedureOperator
    },
    data() {
        return {
            url: "/dashboard/api/procedures",
            operators: [],
            filters: {
                operator: ''
            },
            dialog: false,
            procedure: {},
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    columnName: 'procedures.id',
                    orderable: true
                },
                {
                    label: 'Código',
                    name: 'number',
                    columnName: 'procedures.number',
                    orderable: true
                },
                {
                    label: 'Preço (R$)',
                    name: 'operators',
                    columnName: 'procedure_operators.price',
                    meta: {
                        column: 'price'
                    },
                    component: IndexProcedureOperator,
                    searchable: true,
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
            this.procedure = data || {
                operators: []
            };
            if (!!data) {
                this.request().get(`/procedures/${data.id}`).then(response => {
                    this.procedure.operators = response.data.data;
                }).catch(err => {
                    this.procedure.operators = [];
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
            if (window.confirm("Tem certeza que deseja excluir o procedimento?\nIsso pode causar inconsistências no sistema pois ele pode estar vinculado a uma guia")) {
                this.toggleLoading();
                this.request().delete(`/procedures/${data.id}/delete`).then(response => {
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
