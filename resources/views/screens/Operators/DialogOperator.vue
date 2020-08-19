<template>
  <div class="text-center">
      <v-card>
        <v-card-title class="headline grey lighten-2">
            {{ action === 'store' ? 'Nova operadora' : 'Editar operadora' }}
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
              <v-col cols="12" md="6">
                <v-text-field label="Nome *" required v-model="name"></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="Registro ANS *" required v-model="ans"></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="$emit('action')">Fechar</v-btn>
          <v-btn color="blue darken-1" text @click="salvar">Salvar</v-btn>
        </v-card-actions>
      </v-card>
  </div>
</template>

<script>
export default {
    name: 'dialog-operator',
    props: {
        operator: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            name: '',
            ans: '',
            action: 'store',
            errors: []
        }
    },
    created(){
        this.verifyAction();
    },
    methods: {
        verifyAction() {
            if(this.operator){
                this.name = this.operator.name,
                this.ans = this.operator.ans,
                this.action = 'update'
            } else {
                this.action = 'store'
            }
        },
        salvar() {
            this.errors = [];
            if(this.action === 'store') {
                this.request().post('/operators/store', {
                    name: this.name,
                    ans: this.ans
                }).then(response => {
                    this.$emit('action')
                }).catch(err => {
                    if(err.response.data.errors) {
                        this.errors = err.response.data.errors;
                    }
                });
            } else {
                this.request().put(`/operators/${this.operator.id}/update`, {
                    name: this.name,
                    ans: this.ans
                }).then(response => {
                    this.$emit('action')
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
