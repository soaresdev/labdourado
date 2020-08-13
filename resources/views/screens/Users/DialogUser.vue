<template>
  <div class="text-center">
      <v-card>
        <v-card-title class="headline grey lighten-2">
          {{ action == 'store' ? 'Novo usuário' : 'Editar usuário' }}
        </v-card-title>

        <v-card-text>
          <v-container>
              <v-row v-if="errors">
                  <v-col cols="12">
                      <v-alert
                        dense
                        border="left"
                        type="warning"
                        v-for="error in errors"
                        :key="error"
                        >
                        {{ error }}
                        </v-alert>
                  </v-col>
              </v-row>
            <v-row>
              <v-col cols="12">
                <v-text-field label="Nome *" required v-model="name"></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field label="Nome de usuário *" hint="Usado para logar no sistema" persistent-hint required v-model="username"></v-text-field>
              </v-col>
              <v-col cols="6">
                <v-text-field
                  :label="'Senha' + (action == 'store' ? ' *' : '')"
                  hint="Mínimo 6 caracteres"
                  type="password"
                  persistent-hint
                  required
                  v-model="password"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="$emit('action', 'close')">Fechar</v-btn>
          <v-btn color="blue darken-1" text @click="salvar">Salvar</v-btn>
        </v-card-actions>
      </v-card>
  </div>
</template>

<script>
export default {
    name: 'dialog-user',
    props: {
        user: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            name: '',
            username: '',
            password: '',
            action: 'store',
            errors: []
        }
    },
    created(){
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if(this.user){
                this.name = this.user.name,
                this.username = this.user.username
                this.action = 'update'
            } else {
                this.action = 'store'
            }
        },
        salvar() {
            this.errors = [];
            if(this.action == 'store') {
                this.request().post('/users/store', {
                    name: this.name,
                    username: this.username,
                    password: this.password
                }).then(response => {
                    this.$emit('action', 'save')
                }).catch(err => {
                    if(err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            } else {
                this.request().put(`/users/${this.user.id}/update`, {
                    name: this.name,
                    username: this.username,
                    password: this.password
                }).then(response => {
                    this.$emit('action', 'save')
                }).catch(err => {
                    if(err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            }
        }
    }
}
</script>

<style>

</style>
