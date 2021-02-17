<template>
    <div id="app" class="container">
        <v-card class="mx-auto" max-width="800">
            <v-data-table
            :headers="headers"
            :items="clients">
            
            <template v-slot:top>
                <v-toolbar flat color="white">
                    <v-toolbar-title>Clientes</v-toolbar-title>
                    <v-divider
                        class="mx-4"
                        inset
                        vertical
                    ></v-divider>

                    <v-btn 
                        color="primary"
                        dark
                        class="mb-2"
                        @click="newClientModal()">
                        Nuevo Cliente
                    </v-btn>
                    <v-spacer></v-spacer>

                    <v-btn 
                        color="#388E3C"
                        dark
                        class="mb-2"
                        @click="openImportar = true">
                        Importar Clientes de Excel
                    </v-btn>
                </v-toolbar>
            </template>
                
            <template v-slot:item.actions="{ item }">
                <v-icon
                small
                @click="editModal(item)">
                mdi-pencil
                </v-icon>

                <v-icon
                small
                @click="deleteClient(item.id)">
                mdi-delete
                </v-icon>
            </template>        
            </v-data-table>
        </v-card>        

        <v-divider
            class="mx-4"
            inset
            vertical
        ></v-divider>
        
        <v-card
        v-if="openImportar"
        class="mx-auto"
        max-width="500"
        outlined>
        <v-card-title>Importar</v-card-title>
            <v-col cols="12" sm="6" md="6">
                <v-file-input v-model="file" v-on:change="seleccionarArchivo" label="Importar" outlined dense></v-file-input>
            </v-col>

            <v-col>
                <v-btn @click="importar" class="px-4" color="#388E3C">Importar</v-btn>
                <v-btn color="error" dark @click="openImportar = false">Cerrar</v-btn>
            </v-col>
        </v-card>
        
        <v-row justify="center">
            <v-dialog v-model="addClientDialog" width="500" height="500">
            <v-card>
                <v-card-title>{{titulo}}</v-card-title>
                    <v-form class="px-4">
                        
                        <v-text-field
                        prepend-icon="mdi-account-box"
                        v-model="$v.form.name.$model"
                        label="Nombre"
                        outlined
                        >
                        </v-text-field>
                        <p class="error" v-if="$v.form.name.$error">Nombre requerido</p>
                        
                        <v-text-field
                        prepend-icon="mdi-cellphone-iphone"
                        v-model="$v.form.phone.$model"
                        label="Teléfono"
                        outlined
                        >
                        </v-text-field>
                        <p class="error" v-if="$v.form.phone.$error">Campo numérico de 10 dígitos</p>
                        
                        <v-text-field
                        prepend-icon="mdi-map-marker-radius"
                        v-model="$v.form.address.$model"
                        label="Dirección"
                        outlined
                        >
                        </v-text-field>
                        <p class="error" v-if="$v.form.address.$error">Dirección requerido</p>

                    </v-form>
                    
                <v-card-actions>
                    <v-btn
                    color="success"
                    class="mr-4"
                    @click="editmode ? editClient() : addClient()">
                    Guardar
                    </v-btn>

                    <v-btn
                    color="error"
                    class="mr-4"
                    @click="addClientDialog = false">
                    Cancelar
                    </v-btn>
                </v-card-actions>

                <p class="typo__p" v-if="submitStatus === 'OK'">Ha Agregado un nuevo cliente</p>
                <p class="typo__p" v-if="submitStatus === 'ERROR'">Por favor, llene correctamente el formulario</p>
                <p class="typo__p" v-if="submitStatus === 'PENDING'">Creando...</p>
            </v-card>
        </v-dialog>
        </v-row>
        
    </div>
</template>

<script>
import {required,numeric,maxLength,minLength} from 'vuelidate/lib/validators'
export default {
    created: function(){
        this.listar();
    },
    data(){
        return {
            form: new Form({ id: null, name: null, phone: null, address: null }),
            addClientDialog: false,
            clients: [],
            file: null,
            titulo: '',
            openImportar: false,
            editmode: false,
            editedIndex: -1,
            submitStatus: null,
            headers: [
                { text: 'ID', align: 'start', value: 'id',},
                { text: 'Nombre', value: 'name' },
                { text: 'Dirección', value: 'address' },
                { text: 'Acciones', value: 'actions'}
            ],
        }
    },
    validations:{
        form:{
            name:{
                required
            },
            phone:{
                required,numeric,
                minLength: minLength(10),
                maxLength: maxLength(10),
            },
            address:{
                required
            }
        }
    },
    methods: {
        listar(){
            axios.get('api/clients').then(response => (this.clients = response.data))
        },
        seleccionarArchivo(event){
            this.file = this.$refs.file.files[0]
        },
        importar(){
            if(!this.file){
                this.$swal(
                    'Error',
                    'Seleccione un Archivo',
                    'error'
                )
            }else{
                var data = new FormData();
                data.append('file',this.file)
                axios.post('/api/importar', data,{
                    headers:{
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(() => {
                    this.listar()
                    this.$swal({
                        title: 'Importados',
                        text: 'Clientes Importados Correctamente',
                        icon: 'success'
                    })
                    this.openImportar = false
                }).catch(() =>{
                    this.$swal(
                        'Ocurrio un error',
                        'Los Clientes no fueron importados',
                        'error'
                    )
                })
            }
        },
        newClientModal(){
            this.$v.$reset();
            this.form.reset();
            this.titulo = "Agregando Cliente"
            this.submitStatus = null
            this.addClientDialog = true
            this.editmode = false;
        },
        editModal(client){
            this.$v.$reset();
            this.editedIndex = this.clients.indexOf(client)
            this.form.fill(client);
            this.titulo = "Editando Cliente"
            this.submitStatus = null
            this.addClientDialog = true
            this.editmode = true
        },
        addClient(){
            this.$v.$touch()
            if (this.$v.$invalid) {
                this.submitStatus = 'ERROR'
            }else{
                this.form.post('api/clients').then(() => {
                this.listar();
                this.addClientDialog = false;
                    this.$swal({
                    title: 'Agregado',
                    text: 'cliente agregado',
                    icon: 'success',
                    })
                })
                .catch(() => {
                    this.$swal(
                        'Ocurrio un error',
                        'Cliente no agregado',
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
        editClient(){
            if (this.$v.$invalid) {
                this.submitStatus = 'ERROR'
            }else{
                this.form.put('api/clients/'+this.form.id).then(() => {
                    this.listar();
                    this.addClientDialog = false;
                    this.$swal({
                        title: 'Editado',
                        text: 'El cliente ha sido editado',
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
                        'Ocurrio Un error',
                        'El cliente no ha sido editado',
                        'error'
                    );
                })
            }
        },
        deleteClient(id){
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
                    axios.delete('api/clients/' + id)
                    .then(result => {
                        this.listar();
                        this.$swal({
                            title:'Eliminado!',
                            text:'El cliente ha sido eliminado.',
                            icon:'success',
                        })
                    })
                    .catch(() => {
                        this.$swal(
                            'Ocurrió un error!',
                            'El cliente no ha sido eliminado.',
                            'error'
                        );
                    })
                }
            })
        },
    }
}
</script>