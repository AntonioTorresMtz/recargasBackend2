<?php

namespace App\Http\Controllers;

use App\Models\Servicios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class ServiciosController extends Controller
{
      public function index()
    {
        // Consulta los datos de la tabla 'users'
        $recargas = Servicios::all();

        // Devuelve los datos como una respuesta JSON
        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa',
            'codigo' => 200,
            'data' => $recargas
        ], 200);
    }

        public function ultimoServicio()
    {
        $servicio = Servicios::select('producto', 'referencia', 'cantidad','fecha_plataforma')
            ->orderByDesc('PK_servicio')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Consulta exitosa',
            'codigo' => 200,
            'data' => $servicio
        ], 200);
    }

     public function insertarDatos(Request $request)
    {
        $recarga = Servicios::select('fecha_plataforma')
            ->orderBy('PK_servicio', 'desc')
            ->first();

        $fecha = $recarga->fecha_insercion;

        $request->validate([
            "producto" => "required",
            "referencia" => "required",
            "cantidad" => "required",            
            "fecha" => "required",
        ]);

        if ($fecha != $request["fecha"]) {
            DB::statement(
                "CALL SP_INSERTAR_SERVICIO(?,?,?,?)",
                [
                    $request["producto"],
                    $request["referencia"],
                    $request["cantidad"],
                    $request["fecha"]             
                ]
            );
            $this->imprimir($request);
            return response()->json([
                'success' => true,
                'message' => 'Registro exitoso',
                'codigo' => 201,
            ], 201);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Registro duplicado',
                'codigo' => 200,
            ], 200);
        }
    }

      public function imprimir($data)
    {
        // Crear una instancia del conector de impresión de Windows
        $connector = new WindowsPrintConnector("POS58");

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

        $printer->text("Producto: " . $data['producto'] . "\n");
        $printer->text("Referencia: " . $data['referencia'] . "\n");        
        $printer->text("Monto: $" . number_format($data['monto'], 2, ".", ",") . "\n");
        $printer->text("Fecha: " . $data['fecha'] . "\n");
        $printer->text("Estatus: OK \n");
        $printer->text("\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("Este ticket se imprime de forma automatica unicamente si el pago se hace. ");
        $printer->text("Para dudas o aclaraciones solo con este ticket. \n");
        $printer->text("\n");
        $printer->text("Gracias por su compra :)" . "\n");
        $printer->cut();

        // Cerrar la conexión de impresión
        $printer->close();
    }

}
