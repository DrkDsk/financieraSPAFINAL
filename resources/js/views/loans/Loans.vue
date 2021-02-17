<template>
    <div id="app" class="container">
        <v-card>
            <v-data-table
            :headers="headers"
            :items="loans">

            <template v-slot:item.finished="{ item }">
                <v-chip :color="getStatus(item.finished)" dark>{{ item.finished }}</v-chip>
            </template>
            
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>Préstamos</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>
                    <v-spacer></v-spacer>

                    <v-btn 
                        color="primary"
                        dark
                        class="mb-2"
                        @click="newLoanModal()">
                        Nuevo Préstamo
                    </v-btn>
                </v-toolbar>
            </template>

            <template v-slot:item.finished="{ item }">
                <v-btn :to="{name: 'details',params:{id:to(item)}}" v-if="item.finished" tile outlined color="success">Finalizado</v-btn>
                <v-btn :to="{name: 'details',params:{id:to(item)}}" v-else tile outlined color="warning">Abonar</v-btn>
            </template>

            <template v-slot:item.actions="{ item }">
                
                <v-btn
                color="error"
                fab
                small
                dark>
                    <v-icon 
                    small
                    @click="deleteLoan(item.id)">
                    mdi-delete
                    </v-icon>
                </v-btn>
                
                <v-btn
                    color="primary"
                    fab
                    small
                    dark>
                    <v-icon
                    small
                    @click="editModal(item)">
                    mdi-pencil
                    </v-icon>
                </v-btn>
            </template>
            </v-data-table>
        </v-card>

        <v-row justify="center">
            <v-dialog v-model="addLoanDialog" persistant max-width="700px">
                <v-card>
                    <v-card-title>{{titulo}}</v-card-title>

                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12" sm="6" md="4">
                                    <v-select
                                    prepend-icon="mdi-account-box"
                                    v-model="$v.form.client_id.$model"
                                    v-bind:items="names"
                                    item-text="name"
                                    item-value="id"
                                    label="Nombre"
                                    outlined
                                    >
                                    </v-select>
                            
                                    <p class="error" v-if="$v.form.client_id.$error">Nombre requerido</p>
                                </v-col>
                                
                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                    prepend-icon="mdi-currency-usd"
                                    v-model="$v.form.cantidad.$model"
                                    label="Cantidad"
                                    ref="cantidad"
                                    @change="cuotaPay"
                                    outlined
                                    >
                                    {{this.form.cantidad}}</v-text-field>
                                    <p class="error" v-if="$v.form.cantidad.$error">Cantidad requerida</p>
                                </v-col>

                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                    prepend-icon="mdi-margin"
                                    v-model="$v.form.porcentaje.$model"
                                    label="Porcentaje"
                                    ref="porcentaje"
                                    @change="cuotaPay"
                                    outlined
                                    >
                                    {{this.form.porcentaje}}</v-text-field>

                                    <p class="error" v-if="$v.form.porcentaje.$error">Porcentaje requerido</p>
                                </v-col>

                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                    prepend-icon="mdi-calendar-clock"
                                    v-model="$v.form.numero_de_pagos.$model"
                                    label="Número de Pagos"
                                    ref="pagos"
                                    @change="cuotaPay"
                                    outlined
                                    >
                                    {{this.form.numero_de_pagos}}</v-text-field>
                                    
                                    <p class="error" v-if="$v.form.numero_de_pagos.$error">Número de Pagos es requerido sin decimales</p>
                                    
                                </v-col>

                                <v-col cols="12" sm="6" md="4">
                                    <v-text-field
                                    prepend-icon="mdi-currency-usd"
                                    v-model="$v.form.cuota.$model"
                                    label="Cuota"
                                    ref="cuota"
                                    @change="noPagos"
                                    outlined
                                    >
                                    {{this.form.cuota}}</v-text-field>

                                    <p class="error" v-if="$v.form.cuota.$error">Cuota requerida</p>
                                </v-col>

                                <v-col md="6">
                                    <label for="">Fecha Ministro</label>
                                    <v-date-picker v-model="$v.form.fecha_de_ministro.$model"
                                    prepend-icon="mdi-calendar"
                                    ref="fecha_ministro"
                                    @change="toDate()">
                                    {{this.form.fecha_de_ministro}}
                                    </v-date-picker>

                                    <p class="error" v-if="$v.form.fecha_de_ministro.$error">Fecha de Ministro requerida</p>
                                </v-col>

                                <v-col md="6">
                                    <label for="">Fecha de Vencimiento</label>
                                    <v-date-picker v-model="$v.form.fecha_de_vencimiento.$model"
                                    ref="fecha_vencimiento"
                                    
                                    @change="toDate()">
                                    {{this.form.fecha_de_vencimiento}}</v-date-picker>

                                    <p class="error" v-if="$v.form.fecha_de_vencimiento.$error">Fecha de Vencimiento requerida</p>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                        :disabled="submitStatus === 'PENDING'"
                        color="success"
                        class="mr-4"
                        @click="editmode ? editLoan() : addLoan()">
                        Guardar
                        </v-btn>

                        <v-btn
                        :disabled="submitStatus === 'PENDING'"
                        color="error"
                        class="mr-4"
                        @click="addLoanDialog = false">
                        Cancelar
                        </v-btn>

                    </v-card-actions>

                        <p class="typo__p" v-if="submitStatus === 'OK'">Préstamo Modificado</p>
                        <p class="typo__p" v-if="submitStatus === 'ERROR'">Por favor, llena correctamente el formulario.</p>
                        <p class="typo__p" v-if="submitStatus === 'PENDING'">Modificando...</p>
                </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>

<script>
import { required,minLength, maxLength, between, numeric, integer} from 'vuelidate/lib/validators'

export default {
    created: function(){
        this.listar();
    },
    data(){
        return {
            form: new Form({
                id: null,
                client_id: null,
                cantidad: null,
                porcentaje : null,
                numero_de_pagos: null,
                cuota: null,
                fecha_de_ministro : null,
                fecha_de_vencimiento: null,
            }),
            id: '',
            addLoanDialog: false,
            editedIndex: -1,
            loans: [],
            names: [],
            titulo: '',
            editmode: false,
            submitStatus: null,
            headers: [
                {
                    text: 'ID',
                    align: 'center',
                    sortable: false,
                    value: 'id',
                },
                { text: 'Nombre', align: 'center', value: 'name' },
                { text: 'Cantidad', align: 'center', value: 'amount'},
                { text: 'Número de Pagos', align: 'center', value: 'payments_number'},
                { text: 'Cuota', align: 'center', value: 'fee'},
                { text: 'Fecha de Ministro', align: 'center', value: 'ministry_date'},
                { text: 'Fecha de Vencimiento', align: 'center', value: 'due_date'},
                { text: 'Estado', align: 'center', value:'finished'},
                { text: 'Acciones' , align: 'center', value: 'actions'}
            ]
        }
    },
    
    validations: {
        form:{
            client_id:{
                required
            },
            cantidad:{
                required
            },
            porcentaje:{
                required
            },
            numero_de_pagos: {
                required,
                numeric,
                integer
            },
            cuota: {
                required
            },
            fecha_de_ministro:{
                required
            },
            fecha_de_vencimiento:{
                required
            }
        }
    },

    methods: {
        getStatus(status){
            if(status) return 'green'
            else return 'red'
        },
        listar(){
            axios.get('api/loans').then(response => (this.loans = response.data))
            axios.get('api/loans/create').then(response => (this.names = response.data))
        },
        to(item){
            var index = this.loans.indexOf(item)
            var id = this.loans[index].id
            return id;
        },
        newLoanModal(){
            this.$v.$reset();
            this.form.reset();
            this.titulo = "Creando Préstamo"
            this.submitStatus = null
            this.addLoanDialog = true
            this.editmode = false  
        },
        editModal(loan){
            this.$v.$reset();
            var index = this.loans.indexOf(loan)
            this.id = this.loans[index].id;
            this.form.fill(loan);
            this.titulo = "Editando Pŕestamo"
            this.submitStatus = null
            this.addLoanDialog = true
            this.editmode = true
        },
        addLoan() {
            this.$v.$touch()
            if (this.$v.$invalid) {
                this.submitStatus = 'ERROR'
            }else{
                axios.post('api/loans/',{
                    'porcentaje' : this.form.porcentaje,
                    'client_id' : this.form.client_id,
                    'cantidad' : this.form.cantidad,
                    'numero_de_pagos' : this.form.numero_de_pagos,
                    'cuota' : this.form.cuota,
                    'fecha_de_ministro' : this.form.fecha_de_ministro,
                    'fecha_de_vencimiento' : this.form.fecha_de_vencimiento
                }).then(() => {
                    this.listar();
                    this.addLoanDialog = false;
                    this.$swal({
                        title: 'Agregado',
                        text: 'Préstamo Agregado',
                        icon: 'success',
                        })
                    })
                    .catch(error =>{
                        this.$swal(
                            'Ocurrio un error',
                            'Préstamo no Aregado',
                            'error'
                        );
                    })
                this.submitStatus = 'PENDING'
                setTimeout(() => {
                this.$v.$reset();
                this.submitStatus = 'OK'
                }, 500)
            }
        },
        editLoan(){
            if (this.$v.$invalid) {
                this.submitStatus = 'ERROR'
            }else{
                this.form.put('api/loans/'+ this.id).then(()=>{
                    this.listar();
                    this.addLoanDialog = false
                    this.$swal({
                        title: 'editado',
                        text: 'Préstamo editado',
                        icon: 'success'
                    })
                    this.submitStatus = 'PENDING'
                    setTimeout(() => {
                    this.$v.$reset();
                    this.submitStatus = 'OK'
                    }, 500)
                })
                .catch(() => {
                    this.$swal(
                        'Ocurrio un error',
                        'El Pŕestamo no ha sido modificado',
                        'error'
                    );
                })
            }
        },
        deleteLoan(id){
            this.$swal({
                title: 'Estás Seguro?',
                text: "No podrás deshacer este cambio!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar!'
            }).then((result) => {
                if (result.value) {
                    axios.delete('api/loans/' + id)
                    .then(result => {
                        this.$swal({
                            title:'Eliminado!',
                            text:'El préstamo ha sido eliminado.',
                            icon:'success',
                    })
                    .then(() => {
                        this.listar();
                    });
                })
                    .catch(error => {
                        this.$swal(
                            'Ocurrió un error!',
                            'El préstamo no ha sido eliminado.',
                            'error');
                    });
                }
            })
        },
        cuotaPay(){
            var cantidad = parseInt(this.form.cantidad)
            if(!isNaN(cantidad)) var porcentaje = ((parseInt(this.form.porcentaje) / 100) * cantidad) + cantidad;
            var dias = parseInt(this.form.numero_de_pagos)
            if(!isNaN(dias) && (dias !==0)) this.form.cuota = porcentaje / dias
        },
        noPagos(){
            var cantidad = parseInt(this.form.cantidad)
            if(!isNaN(cantidad)) var porcentaje = ((parseInt(this.form.porcentaje) / 100) * cantidad) + cantidad;
            var cuota = parseInt(this.form.cuota)
            if(!isNaN(cuota) && cuota !==0) this.form.numero_de_pagos = porcentaje / cuota;
        },
        toDate(){
            var format = this.form.fecha_de_ministro.split('-')
            var date = new Date(format[0],format[1]-1,format[2])
            var dias = parseInt(this.form.numero_de_pagos)
            var i = 0;
            
            var weekendDay = verifyWeekend(date,date.getDay())
            
            console.log("date:");
            console.log(date.setDate(weekendDay))
            
            if(dias){
                while(i !== dias){
                    if(date.getDay() == 0 || date.getDay() == 6) {
                        date.setDate(date.getDate()+1)
                    }
                    else{
                        date.setDate(date.getDate()+1)
                        i++
                    }
                }
            }

            var weekendDay = verifyWeekend(date,date.getDay())
        
            date.setDate(weekendDay)
            
            var anio = date.getFullYear()
            var mes = '0' + (date.getMonth()+1)
            var dia = ("0" + date.getDate()).slice(-2)
            var fecha = anio + '-' + mes + '-' + dia
            this.form.fecha_de_vencimiento = fecha            
        },
        verifyWeekend(date, day){
            if(day == 0 || day == 6){
                if(day == 0){
                    console.log('domingo')
                    date.setDate(date.getDate()+1)
                }
                else if (day == 6){
                    console.log('sabado')
                    date.setDate(date.getDate()+2)
                }
            }
            return date.getDate()
        }
    }
}
</script>