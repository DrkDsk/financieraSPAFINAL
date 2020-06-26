<template>
    <div id="app" class="container">
        <v-container fluid>
            <v-row dense>
                <v-col
                :cols="3">
                    <v-card>
                        <v-card-title>Cliente</v-card-title>
                        <v-card-text>
                            <strong>Nombre:</strong>&nbsp;
                            <span>{{this.abono.name}}</span>
                            <v-spacer></v-spacer>
                            
                            <strong>Cuota</strong>
                            <span>{{this.abono.cuota}}</span>
                            <v-spacer></v-spacer>
                            
                            <strong>Total Abonado:</strong>&nbsp;
                            <span>{{this.abono.abonado}}</span>
                            <v-spacer></v-spacer>

                            <strong>Saldo Pendiente:</strong>&nbsp;
                            <span>{{this.abono.pendiente}}</span>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-card v-if="this.abono.pendiente" class="mx-auto" max-width="280">
                    <v-card-title>Abonar</v-card-title>
                    <v-form class="px-2">
                        <v-text-field
                        prepend-icon="mdi-currency-usd"
                        v-model.trim="$v.form.cantidad.$model"
                        label="Cantidad"
                        outlined
                        >
                        </v-text-field>

                        <div class="error" v-if="$v.form.cantidad.$error">{{this.message}}</div>

                        <v-card-actions>
                            <v-btn
                            :disabled="submitStatus === 'PENDING'"
                            small
                            color="primary"
                            class="mr-2"
                            @click="pay()">
                            ABONAR
                            </v-btn>
                        </v-card-actions>

                        <p class="typo__p" v-if="submitStatus === 'ERROR'">Por favor, llena correctamente el formulario.</p>
                        <p class="typo__p" v-if="submitStatus === 'PENDING'">Modificando...</p>

                    </v-form>
                </v-card>
            </v-row>
        </v-container>

        <v-card>
            <v-data-table
            :headers="headers"
            :items="pagos">

            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>Detalles de Pagos</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>
                </v-toolbar>
            </template>
            </v-data-table>
        </v-card>
    </div>
</template>

<script>
import { required,between, minValue, maxValue} from 'vuelidate/lib/validators'

export default {
    created: function(){
        this.listar();
    },
    data(){
        return{
            form : new Form({
                id: null,
                cantidad: ''
            }),
            titulo: "PŔESTAMO COMPLETADO",
            submitStatus: null,
            toPay: null,
            loan_id: null,
            pagos: [],
            abono: [],
            cuota: 0,
            pendiente: 0,
            message: "",
            headers: [
                { text: 'Número de Pago', align: 'center', value: 'numero_pago' },
                { text: 'Cuota', align: 'center', value: 'cuota'},
                { text: 'Abonado', align: 'center', value: 'monto_recibido'},
                { text: 'Fecha de Pago', align: 'center', value: 'fecha_pago'},
                { text: 'Fecha de Abono', align: 'center', value: 'created_at'}
            ]
        }
    },
    validations(){
        return{
            form:{
                cantidad:{
                    minValue: minValue(this.cuota),
                    maxValue: maxValue(this.pendiente)
                }
            }
        }
    },
    methods:{
        listar(){
            this.loan_id = this.$route.params.id;
            this.loan_id = this.loan_id.toString();
            console.log(this.$route.params.id);

            axios.get('/api/payments/'+ this.loan_id).then(response => 
            (this.abono = response.data.abono , this.pagos = response.data.pagos)).then(() =>{
                if(this.abono.cuota > this.abono.pendiente){
                    this.cuota = this.abono.pendiente
                    this.pendiente = this.abono.pendiente
                    this.message = "Ingresa una cantidad igual o menor a: " + this.abono.pendiente
                }
                else{
                    this.cuota = this.abono.cuota
                    this.pendiente = this.abono.pendiente
                    this.message = "Ingrese una cantidad entre: " + this.cuota + " y " + this.pendiente;
                }
            })

        },
        pay(){
            this.$v.$touch()
            if (this.$v.$invalid) {
                this.submitStatus = 'ERROR'
            }else{
                axios.post('/api/payments',{
                    'loan_id' :  this.loan_id,
                    'cantidad' : this.form.cantidad 
                }).then(() =>{
                    this.listar();
                    this.$swal({
                        text: 'Saldo Abonado',
                        icon: 'success'
                    })      
                }).catch(() =>{
                    this.$swal(
                        'Ocurrio un error',
                        'Pago no concretado',
                        'error'
                    );
                })
                this.submitStatus = null
                setTimeout(() => {
                this.$v.$reset();
                }, 500)
            }
        }
    }
}
</script>