<template>
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
                <select
                    v-model="tableData.filters.operator"
                    class="form-control">
                    <option v-for="operator in operators" :key="operator.value" :value="operator.value">{{ operator.text }}</option>
                </select>
            </div>
            <div class="col-md-2 text-right">
                <v-btn class="ma-2" small color="success"  @click="redirect(null)">
                    <v-icon left>mdi-plus</v-icon> Novo
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
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    orderable: true,
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
                handler: this.redirect,
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
        redirect(data) {
            if(data) {
                this.$router.push({name: 'guides-sadt.edit', params: {id: data.id}})
            } else {
                this.$router.push({name: 'guides-sadt.create'})
            }
        },
        close() {
            this.$refs.table.getData();
        },
        delete(data) {
            this.request().delete(`/lots/${data.id}/delete`).then(response => {
                    this.$refs.table.getData();
                });
        }
    },
}
</script>

<style>

</style>
