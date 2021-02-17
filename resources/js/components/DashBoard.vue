<template>
    <v-card
        light
        class="mx-auto">
        <v-container fluid>
            <v-row justify="center">
                <v-col md="4">
                    <v-card
                    dark
                    class="mx-left"
                    color="#0c2141">
                        <v-list-item three-line>
                            <v-icon x-large>mdi-account</v-icon>
                            <v-list-item-content>
                                <v-col class="display-3" cols="6">
                                    {{this.numeroClientes}}
                                </v-col>
                                <v-list-item-subtitle class="text-right">Clientes Totales</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-card>
                </v-col>

                <v-col md="4">
                    <v-card
                    dark
                    class="mx-left"
                    color="#1F7087">
                        <v-list-item three-line>
                            <v-icon x-large>mdi-account</v-icon>
                            <v-list-item-content>
                                <v-col class="display-3" cols="6">
                                    {{this.numeroClientes}}
                                </v-col>
                                <v-list-item-subtitle class="text-right">Clientes Totales</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-card>
                </v-col>
                <v-col md="4">
                    <v-card
                    dark
                    class="mx-left"
                    color="#1F7087">
                        <v-list-item three-line>
                            <v-icon x-large>mdi-account</v-icon>
                            <v-list-item-content>
                                <v-col class="display-3" cols="6">
                                    {{this.numeroClientes}}
                                </v-col>
                                <v-list-item-subtitle class="text-right">Clientes Totales</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>
                    </v-card>
                </v-col> 
            </v-row>
        </v-container>
    </v-card>
</template>

<script>
export default {
    created: function(){
        this.contar();
    },
    data(){
        return{
            numeroClientes: null
        }
    },
    methods: {
        contar(){
            axios.get('api/clients').then(response => (this.numeroClientes = response.data.length))
        }
    }
}
</script>