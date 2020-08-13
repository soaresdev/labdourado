<template>
  <div class="text-center">
      <v-card>
        <v-card-title class="headline grey lighten-2">
          {{ action == 'store' ? 'Novo prestador' : 'Editar prestador' }}
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
              <v-col cols="12">
                <v-text-field label="Código CNES *" hint="Usado nas guias SADT" persistent-hint required v-model="cnes"></v-text-field>
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
    name: 'dialog-provider',
    props: {
        provider: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            name: '',
            cnes: '',
            action: 'store',
            errors: []
        }
    },
    created(){
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if(this.provider){
                this.name = this.provider.name,
                this.cnes = this.provider.cnes
                this.action = 'update'
            } else {
                this.action = 'store'
            }
        },
        salvar() {
            this.errors = [];
            if(this.action == 'store') {
                this.request().post('/providers/store', {
                    name: this.name,
                    cnes: this.cnes
                }).then(response => {
                    this.$emit('action', 'save')
                }).catch(err => {
                    if(err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            } else {
                this.request().put(`/providers/${this.provider.id}/update`, {
                    name: this.name,
                    cnes: this.cnes
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
