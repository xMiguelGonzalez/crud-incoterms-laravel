<?php

namespace App\Utils;

class Utilitat
{
    public static function errorMessage($e)
    {
        $mensaje = '';


        // CUANDO VIENE DE DB


        if (!empty($e->errorInfo[1])) {


            switch ($e->errorInfo[1]) {
                case 2601:
                case 2627:
                case 1062:
                    $mensaje = 'Registro dupplicado';
                    break;
                case 547:
                case 1451:
                    $mensaje = 'Registro con elementos relacionados, no se puede eliminar';
                    break;
                case 515:
                case 1048:
                case 1364:
                    $mensaje = 'Faltan datos obligatorios';
                    break;
                case 8152:
                case 1406:
                    $mensaje = 'El texto introducido es muy largo';
                    break;
                case 245:
                    $mensaje = 'Tipo de dato no válido';
                    break;
                default:
                    $mensaje = $e->errorInfo[1] . ' - ' . $e->errorInfo[2];
                    break;
            }
        } else {


            // EXCEPCIONES GENERALES

            switch ($e->getCode()) {
                case 1044:
                case 1045:
                case 28000:
                    $mensaje = 'Usuario y/o password incorrectos';
                    break;
                case 1049:
                    $mensaje = 'Base de datos desconocida';
                    break;
                case 2002:
                case '08001':
                    $mensaje = 'No se encuentra el servidor';
                    break;
                default:
                    $mensaje = $e->getCode() . ' - ' . $e->getMessage();
                    break;
            }
        }

        return $mensaje;
    }
}
