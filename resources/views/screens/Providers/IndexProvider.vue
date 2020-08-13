<template>
    <div>
        <v-dialog
      v-model="dialog"
      width="500"
    >
        <dialog-provider v-if="dialog" :provider="provider" @action="close"></dialog-provider>
    </v-dialog>
    <data-table
        :columns="columns"
        url="/dashboard/api/providers"
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
            <div class="col-md-6 text-right">
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
import DialogProvider from './DialogProvider'
export default {
    name: 'IndexProvider',
    components: {
        DialogProvider,
    },
    data() {
        return {
            dialog: false,
            provider: {},
            columns: [
                {
                    label: 'ID',
                    name: 'id',
                    orderable: true,
                },
                {
                    label: 'Nome',
                    name: 'name',
                    orderable: true,
                },
                {
                    label: 'CNES',
                    name: 'cnes',
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
            ]
        }
    },
    methods: {
        open(data) {
            this.provider = data;
            this.dialog = true;
        },
        close(data) {
            this.dialog = false;
            this.provider = null;
            if(data == 'save'){
                this.$refs.table.getData();
            }
        },
        delete(data) {
            this.request().delete(`/providers/${data.id}/delete`).then(response => {
                    this.$refs.table.getData();
                });
        }
    },
}
</script>

<style>

</style>
