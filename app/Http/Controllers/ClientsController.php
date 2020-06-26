<?php

namespace App\Http\Controllers;

use App\Imports\importarCliente;
use App\Models\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClientsController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return $clients;
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {

        Client::create([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
        ]);

    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return $client;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        $client = Client::find($id);
        $client->update($request->all());
    }

    public function destroy($id)
    {
        $client = Client::find($id);
        $client->delete();
        return $client;
    }

    public function importarExcel(Request $request){
        
        $file = $request->file('file');
        Excel::import(new importarCliente, $file);
    }

}
