<?php
namespace App\Validation;

class CedulaValidation
{
    /**
     * Valida la estructura matemática de una cédula de identidad ecuatoriana (Módulo 10).
     */
    public function validar_cedula_ec(string $cedula): bool
    {
        $cedula = trim($cedula);

        // Debe ser numérico y de exactamente 10 dígitos
        if (!ctype_digit($cedula) || strlen($cedula) !== 10) {
            return false;
        }

        // Código de provincia (dos primeros dígitos) entre 01 y 24, o 30
        $provincia = (int) substr($cedula, 0, 2);
        if (($provincia < 1 || $provincia > 24) && $provincia !== 30) {
            return false;
        }

        // Tercer dígito debe ser menor a 6 para personas naturales
        $tercerDigito = (int) substr($cedula, 2, 1);
        if ($tercerDigito >= 6) {
            return false;
        }

        // Algoritmo Módulo 10
        $coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
        $digitoVerificador = (int) substr($cedula, 9, 1);
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $valor = (int) $cedula[$i] * $coeficientes[$i];
            if ($valor >= 10) {
                $valor -= 9;
            }
            $suma += $valor;
        }

        $digitoCalculado = (10 - ($suma % 10)) % 10;

        return $digitoCalculado === $digitoVerificador;
    }
}