<?php

namespace App\Http\Controllers;

use App\Models\Recargas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class RecargasController extends Controller
{
    public function index()
    {
        // Consulta los datos de la tabla 'users'
        $recargas = Recargas::all();

        // Devuelve los datos como una respuesta JSON
        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa',
            'codigo' => 200,
            'data' => $recargas
        ], 200);
    }

    public function insertarDatos(Request $request)
    {
        $request->validate([
            "monto" => "required",
            "tipo_recarga" => "required",
            "telefono" => "required",
            "compania" => "required",
            "fecha" => "required",
        ]);

        DB::statement(
            "CALL SP_INSERTAR_RECARGA(?,?,?,?,?)",
            [
                $request["monto"],
                $request["tipo_recarga"],
                $request["telefono"],
                $request["fecha"],
                $request["compania"]

            ]
        );
        $this->imprimir($request);
        return response()->json([
            'success' => true,
            'message' => 'Registro exitoso',
            'codigo' => 201,
        ], 201);
    }

    public function imprimir($data)
    {
        // Crear una instancia del conector de impresión de Windows
        $connector = new WindowsPrintConnector("POS58 Printer");

        // Crear una instancia de la impresora
        $printer = new Printer($connector);

        // Realizar las operaciones de impresión
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        //$printer->setFontSize(2, 2);
        $printer->text("Center Accesories\n");
        $printer->text("Hidalgo #151, Ario de Rosales\n");
        //$printer->text(date('d-m-Y') . "  " . date('H:i:s') . "\n");       
        $printer->text("TICKET DE COMPRA\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $printer->text("Concepto: " . $data['tipo_recarga'] . "\n");
        $printer->text("Compañia: " . $data['compania'] . "\n");
        $printer->text("Numero: " . $data['telefono'] . "\n");
        $printer->text("Monto: $" . number_format($data['monto'], 2, ".", ",") . "\n");
        $printer->text("Fecha: " . $data['fecha'] . '\n');
        $printer->text("Estatus: OK" . '\n');
        $printer->text("\n");

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Gracias por su compra :)\n");
        $printer->cut();

        // Cerrar la conexión de impresión
        $printer->close();
    }
}
