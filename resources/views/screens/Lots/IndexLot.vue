<template>
    <div>
        <v-dialog
            v-model="dialog"
            width="800"
        >
            <dialog-lot v-if="dialog" :operators="operators" :lot="lot" @action="close"></dialog-lot>
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
                    <div class="col-md-3">
                        <input
                            name="name"
                            class="form-control"
                            v-model="tableData.search"
                            placeholder="Pesquisar">
                    </div>
                    <div class="col-md-2">
                        <select
                            v-model="tableData.filters.operator"
                            class="form-control">
                            <option v-for="operator in operators" :key="operator.value" :value="operator.value">
                                {{ operator.text }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select
                            v-model="tableData.filters.closed"
                            class="form-control">
                            <option value>Aberto/Fechado</option>
                            <option value="1">Fechado</option>
                            <option value="0">Aberto</option>
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
import DialogLot from './DialogLot'

export default {
    name: 'IndexLot',
    components: {
        DialogLot
    },
    data() {
        return {
            url: "/dashboard/api/lots",
            operators: [],
            dialog: false,
            lot: {},
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    columnName: 'lots.id',
                    orderable: true,
                },
                {
                    label: 'Nº do Lote',
                    name: 'number',
                    columnName: 'lots.number',
                    orderable: true,
                },
                {
                    label: 'Qtd. de Guias',
                    name: 'guides_count',
                    searchable: false,
                    orderable: false
                },
                {
                    label: 'Total do Lote',
                    name: 'total_formatted',
                    searchable: false,
                    orderable: false
                },
                {
                    label: 'Data de Fechamento',
                    name: 'closed_at',
                    searchable: false,
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
            ],
            filters: {
                operator: '',
                closed: ''
            },
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
            this.lot = data;
            this.dialog = true;
        },
        close() {
            this.dialog = false;
            this.$refs.table.getData();
        },
        delete(data) {
            if (window.confirm("Tem certeza que deseja excluir o lote?\nIsso pode causar inconsistências no sistema pois ele pode estar vinculado a uma guia")) {
                this.request().delete(`/lots/${data.id}/delete`).then(response => {
                    this.$refs.table.getData();
                });
            }
        }
    },
}
</script>

<style>

</style>
