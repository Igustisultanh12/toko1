<?php



namespace App\Http\Controllers\Api;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Sale;

use Illuminate\Support\Facades\Log;



class DokuCallbackController extends Controller

{

    public function handle(Request $request)

    {

        Log::info('DOKU Notification Received:', $request->all());



        // Ambil nomor invoice dari data yang dikirim DOKU

        $invoiceNumber = $request->input('order.invoice_number');

        

        // Cari transaksi di database Bapak

        $sale = Sale::where('transaction_number', $invoiceNumber)->first();



        if ($sale) {

            // UBAH STATUS JADI SUKSES

            $sale->update(['status' => 'success']);

            Log::info("Transaksi {$invoiceNumber} Berhasil Diupdate ke SUCCESS.");

            

            return response()->json(['message' => 'SUCCESS'], 200);

        }



        return response()->json(['message' => 'Transaction Not Found'], 404);

    }

}