<template>
    <v-container fill-height fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" md="3">
                <v-alert
                    border="left"
                    colored-border
                    color="deep-purple accent-4"
                    elevation="2"
                    class="text-center"
                >
                    <v-icon large>mdi-account</v-icon>
                    <p class="headline font-weight-bold">{{ resume.users }}</p>
                    <p class="headline mb-1">{{ resume.users > 1 ? 'Usuários' : 'Usuário'}}</p>
                </v-alert>
            </v-col>
            <v-col cols="12" md="3">
                <v-alert
                    border="left"
                    colored-border
                    color="warning accent-4"
                    elevation="2"
                    class="text-center"
                >
                    <v-icon large>mdi-domain</v-icon>
                    <p class="headline font-weight-bold">{{ resume.providers }}</p>
                    <p class="headline mb-1">{{ resume.providers > 1 ? 'Prestadores' : 'Prestador'}}</p>
                </v-alert>
            </v-col>
            <v-col cols="12" md="3">
                <v-alert
                    border="left"
                    colored-border
                    color="danger accent-4"
                    elevation="2"
                    class="text-center"
                >
                    <v-icon large>mdi-account-group</v-icon>
                    <p class="headline font-weight-bold">{{ resume.patients }}</p>
                    <p class="headline mb-1">{{ resume.patients > 1 ? 'Pacientes' : 'Paciente'}}</p>
                </v-alert>
            </v-col>
            <v-col cols="12" md="3">
                <v-alert
                    border="left"
                    colored-border
                    color="success accent-4"
                    elevation="2"
                    class="text-center"
                >
                    <v-icon large>mdi-doctor</v-icon>
                    <p class="headline font-weight-bold">{{ resume.doctors }}</p>
                    <p class="headline mb-1">{{ resume.doctors > 1 ? 'Médicos' : 'Médico'}}</p>
                </v-alert>
            </v-col>
            <v-col cols="12" md="3">
                <v-alert
                    border="left"
                    colored-border
                    color="pink accent-4"
                    elevation="2"
                    class="text-center"
                >
                    <v-icon large>mdi-domain</v-icon>
                    <p class="headline font-weight-bold">{{ resume.operators }}</p>
                    <p class="headline mb-1">{{ resume.operators > 1 ? 'Operadoras' : 'Operadora'}}</p>
                </v-alert>
            </v-col>
            <v-col cols="12" md="3">
                <v-alert
                    border="left"
                    colored-border
                    color="indigo accent-4"
                    elevation="2"
                    class="text-center"
                >
                    <v-icon large>mdi-paperclip</v-icon>
                    <p class="headline font-weight-bold">{{ resume.lots.length }}</p>
                    <p class="headline mb-1">{{ resume.lots.length > 1 ? 'Lotes' : 'Lote'}}</p>
                </v-alert>
            </v-col>
            <v-col cols="12" md="3">
                <v-alert
                    border="left"
                    colored-border
                    color="teal accent-4"
                    elevation="2"
                    class="text-center"
                >
                    <v-icon large>mdi-clipboard</v-icon>
                    <p class="headline font-weight-bold">{{ resume.guides.length }}</p>
                    <p class="headline mb-1">{{ resume.guides.length > 1 ? 'Guias' : 'Guia'}}</p>
                </v-alert>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="12" md="6">
                <v-card
                    class="mx-auto"
                    color="success"
                    dark
                    max-width="600"
                >
                    <v-card-title>
                        <v-icon
                            large
                            left
                        >
                            mdi-currency-usd
                        </v-icon>
                        <span class="title font-weight-light">Lotes no mês de {{ actual_month }}</span>
                    </v-card-title>
                    <v-card-text class="text-h6 font-weight-bold">
                        <p>Valor de todas as guias abertas: {{ lot.total }}</p>
                        <p>Valor de todas as guias fechadas: {{ lot.total_closed }}</p>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" md="6">
                <v-card
                    class="mx-auto"
                    color="#26c6da"
                    dark
                    max-width="600"
                >
                    <v-card-title>
                        <v-icon
                            large
                            left
                        >
                            mdi-clipboard
                        </v-icon>
                        <span class="title font-weight-light">Ultima guia cadastrada</span>
                    </v-card-title>

                    <v-card-text class="text-h6 font-weight-bold" v-if="resume.guides.length > 0">
                        <p>Nº do Lote: {{ resume.guides[resume.guides.length - 1].lot.number }}</p>
                        <p>Nº Guia no prestador: {{ resume.guides[resume.guides.length - 1].provider_number }}</p>
                        <p>Total Geral: {{ resume.guides[resume.guides.length - 1].total_formatted }}</p>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import moment from 'moment-timezone'
export default {
    name: 'Home',
    data() {
        return {
            logo: Dashboard.app_url + '/images/logo.png',
            resume: {
                users: 0,
                providers: 0,
                patients: 0,
                doctors: 0,
                operators: 0,
                lots: [],
                guides: []
            },
            lot: {},
            actual_month: moment().format('MMMM')
        }
    },
    created() {
        this.getData();
    },
    methods: {
        getData(){
            this.request().get('/resume').then(response => {
                this.resume = response.data.data;
                let formatter = new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
                let total = 0;
                let total_closed = 0;
                this.resume.lots.map(lot => {
                    if(lot.closed_at && moment(lot.closed_at, 'L').format('M') === moment().format('M')) {
                        total_closed += lot.total;
                    } else if(moment(lot.created_at, 'L').format('M') === moment().format('M')) {
                        total += lot.total;
                    }
                })
                this.lot = {
                    total: formatter.format(total),
                    total_closed: formatter.format(total_closed)
                }
            });
        }
    }
}
</script>

<style>

</style>
