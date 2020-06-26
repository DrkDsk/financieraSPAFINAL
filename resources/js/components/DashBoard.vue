<template>
    <v-card 
    class="mx-left"
    max-width="250"
    shaped>
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