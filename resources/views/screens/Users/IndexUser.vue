<template>
    <div>
        <v-dialog
            v-model="dialog"
            width="500"
        >
            <dialog-user v-if="dialog" :user="user" @action="close"></dialog-user>
        </v-dialog>
        <data-table
            :columns="columns"
            :url="url"
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
                    <div class="col-md-6 text-right">
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
import DialogUser from './DialogUser'

export default {
    name: 'IndexUser',
    components: {
        DialogUser,
    },
    data() {
        return {
            url: '/dashboard/api/users',
            dialog: false,
            user: {},
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
                    label: 'Usuário',
                    name: 'username',
                    orderable: true,
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
                    component: ActionsTable,
                },
                {
                    label: '',
                    classes: {
                        color: 'error',
                        icon: 'mdi-delete'
                    },
                    width: 5,
                    orderable: false,
                    searchable: false,
                    event: "click",
                    handler: this.delete,
                    component: ActionsTable,
                },
            ]
        }
    },
    methods: {
        open(data) {
            this.user = data;
            this.dialog = true;
        },
        close(data) {
            this.dialog = false;
            this.user = null;
            this.$refs.table.getData();
        },
        delete(data) {
            if (window.confirm("Tem certeza que deseja excluir o usuário?")) {
                this.request().delete(`/users/${data.id}/delete`).then(response => {
                    this.$refs.table.getData();
                });
            }
        }
    },
}
</script>

<style>

</style>
