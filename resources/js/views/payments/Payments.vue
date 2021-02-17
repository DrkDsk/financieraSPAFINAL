<template>
    <div id="app" class="container">
        <v-card>
            <v-data-table
            :headers="headers"
            :items="loans"> 

            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>Pagos</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>

                    <v-btn 
                        color="#388E3C"
                        dark
                        class="mb-2"
                        @click="exportarExcel()">
                        Exportar Pagos a Excel
                    </v-btn>

                </v-toolbar>
            </template>

            <template v-slot:item.actions="{ item }">
                <v-btn color="info" icon :to="{name: 'details',params:{id:item.id}}">
                    <v-icon
                    small
                    >
                    mdi-eye
                    </v-icon>
                </v-btn>
            </template>

            </v-data-table>
        </v-card>
    </div>
</template>

<script>

export default {
    created: function(){
        this.listar();
    },
    data(){
        return {
            id: '',
            addLoanDialog: false,
            loans: [],
            names: [],
            submitStatus: null,
            headers: [
                {
                    text: 'ID',
                    align: 'center',
                    sortable: false,
                    value: 'id',
                },
                { text: 'Nombre', align:'center', value: 'name' },
                { text: 'Cantidad', align:'center', value: 'amount' },
                { text: 'Número de Pagos', align:'center', value: 'payments_number'},
                { text: 'Cuota', align:'center', value: 'fee'},
                { text: 'Pagos Completados', align:'center', value: 'pagos_completados'},
                { text: 'Saldo Abonado' , align:'center', value: 'saldo_abonado'},
                { text: 'Saldo Pendiente', align:'center', value: 'saldo_pendiente'},
                { text: 'Acciones', align:'center', value: 'actions'}
            ]
        }
    },

    methods: {
        listar(){
            axios.get('api/payments').then(response => (this.loans = response.data))
        },
        exportarExcel(){
            window.open('http://127.0.0.1:8000/api/exportar','_blank');
        }
    }
}
</script>