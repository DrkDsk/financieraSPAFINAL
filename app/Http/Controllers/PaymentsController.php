<?php

namespace App\Http\Controllers;

use App\Exports\exportExcel;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Loan;
use App\Models\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaymentsController extends Controller
{
    public function index()
    {
        $loans = Loan::join('clients','loans.client_id','=','clients.id')->select('loans.*','clients.name')->get();
        return $loans;
    }

    public function create($id)
    {
    }
    
    public function store(Request $request)
    {   
        $id = $request->input('loan_id');
        $cuota = Loan::find($id)->fee;
        $total = Loan::find($id)->total_pay;
        $recibido = Payment::where('loan_id','=',$id)->sum('monto_recibido');
        $pendiente = $total - $recibido;

        $pagos = Payment::all()->where('loan_id','=',$id);
        $ultimoAbono = Payment::where('loan_id','=',$id)->select('monto_recibido')->get()->last();
        $ultimoAbono = $ultimoAbono->monto_recibido;

        $ultimoPago = Payment::where('loan_id','=',$id)->select('numero_pago')->get()->last();
        $ultimoPago = $ultimoPago->numero_pago;
        $monto = intval($request->input('cantidad'));

        foreach ($pagos as $pago){
            if($monto > $pago->cuota && $pago->numero_pago != $ultimoPago){
                if($pago->monto_recibido == 0){
                    $pago->monto_recibido = $pago->cuota;
                    $pago->paid = 1;
                    $pago->save();
                    $monto = $monto - $cuota;
                }
                //representa el abono del ultimo pago
                elseif($monto > $pago->cuota && $pago->numero_pago == $ultimoPago){
                    $pago->paid = 1;
                    $pago->monto_recibido = $monto;
                    $pago->save();
                break;
                }
            }
            elseif($monto == $cuota){
                //representa el espacio disponible para abonar, si hay un cero
                //significa que hay un espacio libre para abonar
                if($pago->monto_recibido == 0){
                    $pago->monto_recibido = $monto;
                    $pago->paid = 1;
                    $pago->save();
                    
                    break;   
                }
            }
            else{
                if($pago->monto_recibido == 0){
                    $pago->monto_recibido = $monto;
                    $pago->save();
                    $monto = 0;
                }
                elseif($pago->numero_pago == $ultimoPago && $pago->monto_recibido !=0){
                    $abono = $ultimoAbono + intval($request->input('cantidad'));
                    $pago->monto_recibido = $abono;
                    $pago->save();
                }
            } 
        }

        $suma = Payment::where('loan_id','=',$id)->sum('monto_recibido');
        $loan = Loan::find($id)->total_pay;
        
        if($suma == $loan){
            $loan = Loan::find($id);
            $loan->finished = 1;
            $loan->save();
        }
    }

    public function show($id)
    {
        //obtiene el nombre del cliente
        $abonado = Loan::find($id)->client_id;
        $abonado = Client::find($abonado)->name;
        $consultaTotal = Loan::where('id','=',$id)->select
        ('loans.total_pay','loans.fee','loans.payments_number','loans.ministry_date','loans.due_date')->get();
        
        $suma = Payment::where('loan_id','=',$id)->sum('monto_recibido');
        $pagos = Payment::where('loan_id','=',$id)->select('payments.*')->get();
        
        $total = $consultaTotal[0]->total_pay;
        $cuota = $consultaTotal[0]->fee;
        $pendiente = $total - $suma;
        $pendiente = intval($pendiente);
        $cuota = intval($cuota);

        $loan = Loan::find($id)->total_pay;

        //almacena 1 si ya acabo de pagar
        if($suma == $loan){
            $loan = Loan::find($id);
            $loan->finished = 1;
            $loan->save();
        }
        $payment = array(
            'name' => $abonado,
            'cuota' => $cuota,
            'id' => $id,
            'abonado' => $suma,
            'pendiente' => $pendiente
        );
        return [
            'abono' => $payment, 
            'pagos' => $pagos
        ];
    }

    public function edit($id)
    {
        return view('payments.pay',[
            'payment' => $id
        ]);
    }

    public function update(Request $request, $id)
    {
        $request -> validate([
            'amount' => 'required',
        ]);

        $payment = Payment::find($id);
        $payment->update($request->all());
        
        return redirect()->route('payments.index');
    }

    public function exportExcel(){
        return Excel::download(new exportExcel, 'resumen-pagos.xlsx');
    }

    public function destroy($id)
    {
        
    }
}
