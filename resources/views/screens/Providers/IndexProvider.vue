<template>
    <div>
        <v-dialog
            v-model="dialog"
            width="800"
        >
            <dialog-provider v-if="dialog" :operators="operators" :provider="provider"
                             @action="close"></dialog-provider>
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
import DialogProvider from './DialogProvider'
import IndexProviderOperator from './IndexProviderOperator'

export default {
    name: 'IndexProvider',
    components: {
        DialogProvider,
        IndexProviderOperator
    },
    data() {
        return {
            url: "/dashboard/api/providers",
            operators: [],
            filters: {
                operator: ''
            },
            dialog: false,
            provider: {},
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    columnName: 'providers.id',
                    orderable: true
                },
                {
                    label: 'Nome',
                    name: 'name',
                    columnName: 'providers.name',
                    orderable: true
                },
                {
                    label: 'CNES',
                    name: 'cnes',
                    columnName: 'providers.cnes',
                    orderable: true
                },
                {
                    label: 'Cód. na Operadora',
                    name: 'operators',
                    columnName: 'provider_operators.provider_operator_number',
                    meta: {
                        column: 'provider_operator_number'
                    },
                    component: IndexProviderOperator,
                    orderable: true
                },
                {
                    label: '',
                    classes: {
                        color: 'primary',
                        icon: 'mdi-pencil'
                    },
                    width: 5,
                    orderable: false,
                    searchable: false,
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
                    searchable: false,
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
            this.provider = data || {
                operators: []
            };
            if (!!data) {
                this.request().get(`/providers/${data.id}`).then(response => {
                    this.provider.operators = response.data.data;
                }).catch(err => {
                    this.provider.operators = [];
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
            if (window.confirm("Tem certeza que deseja excluir o prestador?\nIsso pode causar inconsistências no sistema pois ele pode estar vinculado a uma guia")) {
                this.toggleLoading();
                this.request().delete(`/providers/${data.id}/delete`).then(() => {
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
