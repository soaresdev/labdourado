<template>
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
                    <v-btn class="ma-2" small color="success" @click="redirect(null)">
                        <v-icon left>mdi-plus</v-icon>
                        Novo
                    </v-btn>
                </div>
            </div>
        </div>
    </data-table>
</template>

<script>
import ActionsTable from '../../assets/js/components/ActionsTable'

export default {
    name: 'IndexGuideSadt',
    data() {
        return {
            url: "/dashboard/api/guides-sadt",
            operators: [],
            filters: {
                operator: ''
            },
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    columnName: 'guide_sadts.id',
                    orderable: true,
                },
                {
                    label: 'Nº do Lote',
                    name: 'lot.number',
                    columnName: 'lots.number',
                    orderable: false
                },
                {
                    label: 'Nº Guia no Prestador',
                    name: 'provider_number',
                    columnName: 'guide_sadts.provider_number',
                    orderable: false
                },
                {
                    label: 'Senha',
                    name: 'password',
                    columnName: 'guide_sadts.password',
                    orderable: false
                },
                {
                    label: 'Total',
                    name: 'total_formatted',
                    columnName: 'guide_sadts.total',
                    orderable: true
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
                    handler: this.redirect,
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
        redirect(data) {
            if (data) {
                this.$router.push({name: 'guides-sadt.edit', params: {id: data.id}})
            } else {
                this.$router.push({name: 'guides-sadt.create'})
            }
        },
        close() {
            this.$refs.table.getData();
        },
        delete(data) {
            if (window.confirm("Tem certeza que deseja excluir a guia?\n")) {
                this.toggleLoading();
                this.request().delete(`/guides-sadt/${data.id}/delete`).then(response => {
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
