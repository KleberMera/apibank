<?php
class SemanaPar_ctrl
{
    public $M_SemanaParticipante = null;
    public function __construct()
    {
        $this->M_SemanaParticipante = new M_SemanaParticipante();
    }

    
    public function insertarSemanaParticipante($f3)
{
    $mensaje = "";
    $retorno = 0;

    $sp_partId = $f3->get('POST.sp_partId');
    $sp_Snombre = $f3->get('POST.sp_Snombre');
    $sp_valor = $f3->get('POST.sp_valor');
    $sp_fecha = $f3->get('POST.sp_fecha');
    $sp_responsable = $f3->get('POST.sp_responsable');
    $sp_prestamo = $f3->get('POST.sp_prestamo');
    $sp_interep = $f3->get('POST.sp_interesp');
    $sp_prestamofecha = $f3->get('POST.sp_prestamofecha');
    $sp_estado = $f3->get('POST.sp_estado');

    // Buscar el registro por sp_Snombre y sp_partId
    $this->M_SemanaParticipante->load(['sp_Snombre=? AND sp_partId=?', $sp_Snombre, $sp_partId]);

    if ($this->M_SemanaParticipante->loaded()) {
        // El registro ya existe, actualizar los datos
        $this->M_SemanaParticipante->set('sp_prestamo', $sp_prestamo);
        $this->M_SemanaParticipante->set('sp_interesp', $sp_interep);
        $this->M_SemanaParticipante->set('sp_prestamofecha', $sp_prestamofecha);
        $this->M_SemanaParticipante->save();

        $mensaje = "Prestamo del Participante registrado correctamente";
        $retorno = 1;
    } else {
        // El registro no existe, crear uno nuevo
        $this->M_SemanaParticipante->set('sp_partId', $sp_partId);
        $this->M_SemanaParticipante->set('sp_Snombre', $sp_Snombre);
        $this->M_SemanaParticipante->set('sp_valor', $sp_valor);
        $this->M_SemanaParticipante->set('sp_fecha', $sp_fecha);
        $this->M_SemanaParticipante->set('sp_responsable', $sp_responsable);
        $this->M_SemanaParticipante->set('sp_prestamo', $sp_prestamo);
        $this->M_SemanaParticipante->set('sp_interesp', $sp_interep);
        $this->M_SemanaParticipante->set('sp_prestamofecha', $sp_prestamofecha);
        $this->M_SemanaParticipante->set('sp_estado', $sp_estado);
        $this->M_SemanaParticipante->save();

        $mensaje = "Participante registrado correctamente";
        $retorno = 1;
    }

    // Devolver la respuesta en formato JSON
    echo json_encode([
        'mensaje' => $mensaje,
        'retorno' => $retorno
    ]);
}


    
    public function obtenerSemanasPorParticipante($f3)
    {
        $sp_partId = $f3->get('POST.sp_partId');
    
        // Cargar todos los registros de semanaparticipante filtrados por sp_partId
        $resultados = $this->M_SemanaParticipante->find(['sp_partId=?', $sp_partId]);
    
        // Preparar un array para almacenar los resultados
        $semanas = [];
    
        // Iterar sobre los resultados y guardar en el array
        foreach ($resultados as $resultado) {
            $semanas[] = [
                'sp_partId' => $resultado->get('sp_partId'),
                'sp_Snombre' => $resultado->get('sp_Snombre'),
                'sp_valor' => $resultado->get('sp_valor'),
                'sp_fecha' => $resultado->get('sp_fecha'),
                'sp_responsable' => $resultado->get('sp_responsable'),
                'sp_prestamo' => $resultado->get('sp_prestamo'),
                'sp_interesp' => $resultado->get('sp_interesp'),
                'sp_prestamofecha' => $resultado->get('sp_prestamofecha'),
                'sp_estado' => $resultado->get('sp_estado'),
                'estadoprestamo' => $resultado->get('estadoprestamo')
            ];
        }
    
        // Devolver la respuesta en formato JSON
        echo json_encode([
            'semanas' => $semanas
        ]);
    }

    public function actualizarEstadoPrestamo($f3) {
        $partId = $f3->get('POST.sp_partId');
        $nombreSemana = $f3->get('POST.sp_Snombre');
    
        // Acceder a la conexión de la base de datos a través de F3
        $db = $f3->get('DB');
    
        // Agregar un espacio al principio de $nombreSemana para la consulta de total_pagos
        $nombreSemanaConEspacio = ' ' . $nombreSemana;
    
        // Obtener los valores de préstamo e interés de semana_participante
        $querySelect = "SELECT sp_prestamo, sp_interesp
                        FROM semana_participante
                        WHERE sp_partId = ? AND sp_Snombre = ?";
    
        $stmtSelect = $db->exec($querySelect, [$partId, $nombreSemana]);
        $sp_prestamo = $stmtSelect[0]['sp_prestamo'] ?? 0;
        $sp_interesp = $stmtSelect[0]['sp_interesp'] ?? 0;
    
        // Obtener la suma de pagos realizados
        $queryTotalPagos = "SELECT SUM(pp_pagos) AS total_pagos
                            FROM prestamos_participante
                            WHERE pp_partId = ? AND pp_semana = ?";
        
        $stmtTotalPagos = $db->exec($queryTotalPagos, [$partId, $nombreSemanaConEspacio]);
        $totalPagos = $stmtTotalPagos[0]['total_pagos'] ?? 0;
    
        // Calcular el total de la deuda y el restante de la deuda
        $totalDeuda = $sp_prestamo + $sp_interesp;
        $restanteDeuda = $totalDeuda - $totalPagos;
    
        // Actualizar estadoprestamo en semana_participante y restante_Deuda
        $queryUpdate = "UPDATE semana_participante
                        SET estadoprestamo = CASE
                            WHEN ? >= ? THEN 'C'
                            ELSE 'P'
                        END,
                        restante_Deuda = ?
                        WHERE sp_partId = ? AND sp_Snombre = ?";
    
        // Ejecutar la actualización con los parámetros necesarios
        $db->exec($queryUpdate, [$totalPagos, $totalDeuda, $restanteDeuda, $partId, $nombreSemana]);
    
        // Obtener el nuevo valor de estadoprestamo
        $queryNewEstado = "SELECT estadoprestamo, restante_Deuda
                           FROM semana_participante
                           WHERE sp_partId = ? AND sp_Snombre = ?";
    
        $stmtNewEstado = $db->exec($queryNewEstado, [$partId, $nombreSemana]);
        $nuevoEstado = $stmtNewEstado[0]['estadoprestamo'] ?? 'P';
        $nuevoRestanteDeuda = $stmtNewEstado[0]['restante_Deuda'] ?? 0;
    
        // Devolver la respuesta en formato JSON con el nuevo estado y restante_Deuda
        echo json_encode([
            'mensaje' => 'Estado de préstamo actualizado',
            'estadoprestamo' => $nuevoEstado,
            'restante_Deuda' => $nuevoRestanteDeuda
        ]);
    }
    
    public function obtenerTotalSemana($f3){
        $sp_Snombre = $f3->get('POST.sp_Snombre');
        $cadenaSql = "SELECT SUM(sp_valor) AS total_semana FROM semana_participante WHERE sp_Snombre = '$sp_Snombre'";
        $result = $f3->DB->exec($cadenaSql);
        $total_semana = $result[0]['total_semana'];
        echo json_encode([
            'mensaje' => 'Total de la semana obtenido correctamente',
            'total_semana' => $total_semana
        ]);
    }

    public function calcular_SaldoAnterior($f3) {
        $sp_Snombre = $f3->get('POST.sp_Snombre');
        
        // Calcular el nombre de la semana anterior
        $numeroSemanaActual = (int) filter_var($sp_Snombre, FILTER_SANITIZE_NUMBER_INT);
        $numeroSemanaAnterior = $numeroSemanaActual - 1;
        $sp_SnombreAnterior = "Semana " . $numeroSemanaAnterior;
    
        // 1. Obtener el saldo anterior utilizando el nombre de la semana anterior
        $saldoAnteriorCtrl = new SaldoAnterior();
        
        // Simular una solicitud GET para obtener el saldo de la semana anterior
        $f3->set('PARAMS.tsa_semana', $sp_SnombreAnterior);
        ob_start(); 
        $saldoAnteriorCtrl->obtenerSaldoAnterior($f3);
        $saldoAnteriorResponse = json_decode(ob_get_clean(), true);
    
        $saldoAnterior = $saldoAnteriorResponse['saldo_anterior'] ?? 0;
    
        // 2. Obtener el total de la semana actual para todos los participantes
        ob_start();
        $this->obtenerTotalSemana($f3);
        $totalSemanaResponse = json_decode(ob_get_clean(), true);
        $totalSemana = $totalSemanaResponse['total_semana'] ?? 0;
    
        // 3. Obtener los valores de préstamo, interés y estado de préstamo para cada participante en la semana actual
        $db = $f3->get('DB');
        $query = "SELECT sp_prestamo, sp_interesp, estadoprestamo 
                  FROM semana_participante 
                  WHERE sp_Snombre = ?";
        $result = $db->exec($query, [$sp_Snombre]);
    
        $totalPrestamo = 0;
        $totalInteresp = 0;
    
        foreach ($result as $row) {
            $totalPrestamo += $row['sp_prestamo'] ?? 0;
            // Solo sumar el interés si el estado del préstamo es "C"
            if ($row['estadoprestamo'] == 'C') {
                $totalInteresp += $row['sp_interesp'] ?? 0;
            }
        }
    
        // 4. Calcular el saldo anterior con la fórmula corregida
        $saldoAnteriorCalculado = $saldoAnterior + $totalSemana + $totalInteresp - $totalPrestamo;
    
        // 5. Devolver el saldo anterior calculado
        echo json_encode([
            'mensaje' => 'Saldo anterior calculado correctamente',
            'saldo_anterior_calculado' => $saldoAnteriorCalculado
        ]);
    }
    

    public function insertarSaldoAnterior($f3) {
        $tsa_semana = $f3->get('POST.tsa_semana');
        $tsa_SaldoAnterior = $f3->get('POST.tsa_SaldoAnterior');

        $cadenaSql = "UPDATE tabla_saldoanterior SET tsa_SaldoAnterior = ? WHERE tsa_semana = ?";
        $f3->DB->exec($cadenaSql, [$tsa_SaldoAnterior, $tsa_semana]);

        echo json_encode([
            'mensaje' => 'Saldo anterior registrado correctamente'
        ]);
    }
    
    public function verificarPagoAtrasado($f3)
    {
        $sp_partId = $f3->get('POST.sp_partId');
        $sp_Snombre = $f3->get('POST.sp_Snombre');
        $sp_espacioSnombre = ' ' . $sp_Snombre;
    
        // Obtener la fecha de vencimiento del préstamo (sp_prestamofecha) de semana_participante
        $queryVencimiento = "SELECT sp_prestamofecha FROM semana_participante WHERE sp_partId = ? AND sp_Snombre = ?";
        $db = $f3->get('DB');
        $stmtVencimiento = $db->exec($queryVencimiento, [$sp_partId, $sp_Snombre]);
        $fechaVencimiento = $stmtVencimiento[0]['sp_prestamofecha'] ?? null;
    
        if (!$fechaVencimiento) {
            echo json_encode([
                'mensaje' => 'Fecha de vencimiento no encontrada',
                'atrasado' => false
            ]);
            return;
        }
    
        // Obtener la última fecha de pago (pp_fecha) de prestamos_participante
        $queryUltimoPago = "SELECT pp_fecha FROM prestamos_participante WHERE pp_partId = ? AND pp_semana = ? ORDER BY pp_fecha DESC LIMIT 1";
        $stmtUltimoPago = $db->exec($queryUltimoPago, [$sp_partId, $sp_espacioSnombre]);
        $ultimaFechaPago = $stmtUltimoPago[0]['pp_fecha'] ?? null;
    
        if (!$ultimaFechaPago) {
            echo json_encode([
                'mensaje' => 'No se encontró registro de pagos para este participante y semana',
                'atrasado' => true
            ]);
            return;
        }
    
        // Comparar la fecha de vencimiento con la última fecha de pago
        $atrasado = strtotime($ultimaFechaPago) > strtotime($fechaVencimiento) + (4 * 7 * 24 * 60 * 60); // 4 semanas
    
        // Devolver el resultado en formato JSON
        echo json_encode([
            'mensaje' => $atrasado ? 'El pago está atrasado' : 'El pago no está atrasado',
            'atrasado' => $atrasado
        ]);
    }
    
    public function obtenerDatosPorSemanas($f3)
    {
        // 1. Obtener todas las semanas desde tabla_saldoanterior donde tsa_SaldoAnterior no sea 0.00
        $querySemanas = "SELECT tsa_semana 
                         FROM tabla_saldoanterior 
                         WHERE tsa_SaldoAnterior != 0.00";
        $db = $f3->get('DB');
        $resultSemanas = $db->exec($querySemanas);
        
        $datos = [];
    
        foreach ($resultSemanas as $rowSemana) {
            $semana = $rowSemana['tsa_semana'];
    
            // 2. Obtener el total de la semana desde semana_participante
            $queryTotalSemana = "SELECT SUM(sp_valor) AS total_semana 
                                 FROM semana_participante 
                                 WHERE sp_Snombre = ?";
            $resultTotalSemana = $db->exec($queryTotalSemana, [$semana]);
            $totalSemana = $resultTotalSemana[0]['total_semana'] ?? 0;
    
            // 3. Obtener los préstamos y participantes de la semana
            $queryPrestamos = "SELECT sp_prestamo, p.part_nombre 
                               FROM semana_participante sp 
                               JOIN participante p ON sp.sp_partId = p.part_id 
                               WHERE sp.sp_Snombre = ?";
            $resultPrestamos = $db->exec($queryPrestamos, [$semana]);
    
            // 4. Obtener el total de préstamos de la semana
            $queryTotalPrestamos = "SELECT SUM(sp_prestamo) AS total_prestamos 
                                    FROM semana_participante 
                                    WHERE sp_Snombre = ?";
            $resultTotalPrestamos = $db->exec($queryTotalPrestamos, [$semana]);
            $totalPrestamos = $resultTotalPrestamos[0]['total_prestamos'] ?? 0.0;
    
            // 5. Obtener el saldo anterior de la semana
            $querySaldoAnterior = "SELECT tsa_SaldoAnterior 
                                   FROM tabla_saldoanterior 
                                   WHERE tsa_semana = ?";
            $resultSaldoAnterior = $db->exec($querySaldoAnterior, [$semana]);
            $saldoAnterior = $resultSaldoAnterior[0]['tsa_SaldoAnterior'] ?? 0.0;
    
            // 6. Preparar los datos en el formato requerido
            $prestamos = [];
            foreach ($resultPrestamos as $rowPrestamo) {
                if ($rowPrestamo['sp_prestamo'] > 0) {
                    $prestamos[] = [
                        'prestamo' => $rowPrestamo['sp_prestamo'],
                        'participante' => $rowPrestamo['part_nombre']
                    ];
                }
            }
    
            $datos[] = [
                'Semana' => $semana,
                'TotalSemana' => $totalSemana,
                'TotalPrestamos' => $totalPrestamos,
                'Prestamos' => $prestamos,
                'SaldoAnterior' => $saldoAnterior
            ];
        }
    
        // 7. Devolver la respuesta en formato JSON
        echo json_encode([
            'mensaje' => 'Datos obtenidos correctamente',
            'datos' => $datos
        ]);
    }

    public function obtenerParticipantesSinCancelacion($f3)
    {
        $sp_Snombre = $f3->get('POST.sp_Snombre'); // Nombre de la semana proporcionado
    
        // Obtener todos los participantes
        $db = $f3->get('DB');
        $queryParticipantes = "SELECT * FROM participante";
        $participantes = $db->exec($queryParticipantes);
    
        if (!$participantes) {
            echo json_encode([
                'mensaje' => 'No se encontraron participantes',
                'participantes' => []
            ]);
            return;
        }
    
        // Obtener los participantes que ya están en la tabla semana_participante para la semana dada
        $queryParticipantesSemana = "SELECT DISTINCT sp_partId FROM semana_participante WHERE sp_Snombre = ?";
        $participantesSemana = $db->exec($queryParticipantesSemana, [$sp_Snombre]);
        
        $participantesSemanaIds = array_column($participantesSemana, 'sp_partId');
        
        // Filtrar los participantes que no están en la tabla semana_participante
        $participantesSinCancelacion = array_filter($participantes, function($participante) use ($participantesSemanaIds) {
            return !in_array($participante['part_id'], $participantesSemanaIds);
        });
    
        // Devolver los resultados en formato JSON
        echo json_encode([
            'mensaje' => 'Participantes sin cancelación obtenidos correctamente',
            'participantes_sin_cancelacion' => array_values($participantesSinCancelacion)
        ]);
    }
    
    
}