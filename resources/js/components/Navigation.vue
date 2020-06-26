<template>
    <v-list>
        <v-list-item dark to="/dashboard">
            <v-list-item-icon>
                <v-icon>mdi-view-dashboard</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Dashboard</v-list-item-title>
        </v-list-item>
        
        <v-list-item dark to="/about">
            <v-list-item-icon>
                <v-icon>mdi-more</v-icon>
            </v-list-item-icon>
            <v-list-item-title>About</v-list-item-title>
        </v-list-item>

        <v-list-item dark to="/clients">
            <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Clientes</v-list-item-title>
        </v-list-item>

        <v-list-item dark to="/loans">
            <v-list-item-icon>
                <v-icon>mdi-chart-histogram</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Préstamos</v-list-item-title>
        </v-list-item>

        <v-list-item dark to="/payments">
            <v-list-item-icon>
                <v-icon>mdi-chart-line</v-icon>
            </v-list-item-icon>
            <v-list-item-title>Pagos</v-list-item-title>
        </v-list-item>
    </v-list>
</template>