<template>
    <div>
        <v-dialog
            v-model="dialog"
            width="500"
        >
            <dialog-operator v-if="dialog" :operator="operator" @action="close"></dialog-operator>
        </v-dialog>
        <data-table
            :columns="columns"
            :url="url"
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
                    <div class="col-md-2 text-right">
                        <v-btn class="ma-2 text-decoration-none" small color="info" href="/dashboard/api/export/operators">
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
import DialogOperator from './DialogOperator'

export default {
    name: 'IndexOperator',
    components: {
        DialogOperator,
    },
    data() {
        return {
            url: "/dashboard/api/operators",
            dialog: false,
            operator: {},
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    orderable: true
                },
                {
                    label: 'Nome',
                    name: 'name',
                    orderable: true
                },
                {
                    label: 'Registro ANS',
                    name: 'ans',
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
    methods: {
        open(data) {
            this.operator = data;
            this.dialog = true;
        },
        close() {
            this.dialog = false;
            this.$refs.table.getData();
        },
        delete(data) {
            if (window.confirm("Tem certeza que deseja excluir a operadora?\nIsso pode causar inconsistências no sistema pois ele pode estar vinculado a uma guia")) {
                this.toggleLoading();
                this.request().delete(`/operators/${data.id}/delete`).then(response => {
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
