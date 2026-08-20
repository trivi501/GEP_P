<?php

namespace App\Services;

use App\Models\Pago;

class PagoFacturacionDataService
{
    public static function eagerLoad()
    {
        return [
            'cuentasPagos.cuenta',
            'formasPagosCada.formaPago',
            'historialCaja.caja',
            'historialCaja.cajero.usuario',
            'predio.calle',
            'predio.colonia',
            'predio.contribuyente.tipoContribuyente',
            'predio.contribuyente.domicilio',
            'predio.contribuyente.datosFacturacion.domicilioFacturacion',
            'predio.contribuyente.datosFacturacion.regimenFiscal',
            'user',
        ];
    }

    public static function build(Pago $pago): array
    {
        $contribuyente = $pago->predio?->contribuyente;
        $datosFacturacion = $contribuyente?->datosFacturacion?->first();

        return [
            'folio' => $pago->folio,
            'fecha' => $pago->fecha,
            'estatus' => $pago->estatus,
            'tipo_pago' => $pago->tipo_pago,
            'descripcion' => $pago->descripcion,
            'monto' => (float) $pago->monto,
            'descuento' => (float) $pago->descuento,
            'anio_pago' => $pago->anio_pago,

            'contribuyente' => [
                'nombre' => $pago->nombre,
                'rfc' => $pago->rfc,
                'cuenta' => $contribuyente?->cuenta,
                'nombre_completo' => $contribuyente?->nombre_completo,
                'nombre_moral' => $contribuyente?->nombre_moral,
                'tipo_contribuyente' => $contribuyente?->tipoContribuyente?->area_contribuyente ?? $contribuyente?->tipoContribuyente?->descripcion ?? null,
                'telefono' => $contribuyente?->telefono,
                'correo_electronico' => $contribuyente?->correo_electronico,
                'domicilio' => $contribuyente?->domicilio?->domicilio_completo,
            ],

            'datos_facturacion' => $datosFacturacion ? [
                'rfc_facturacion' => $datosFacturacion->rfc_facturacion,
                'razon_social' => $datosFacturacion->razon_social,
                'correo' => $datosFacturacion->correo,
                'codigo_postal_fiscal' => $datosFacturacion->CP_DomicilioFiscal_contribuyente,
                'regimen_fiscal' => $datosFacturacion->regimenFiscal ? [
                    'clave_sat' => $datosFacturacion->regimenFiscal->c_RegimenFiscal,
                    'descripcion' => $datosFacturacion->regimenFiscal->{'Descripción'},
                ] : null,
                'domicilio_fiscal' => $datosFacturacion->domicilioFacturacion?->domicilio_completo,
            ] : null,

            'predio' => $pago->predio ? [
                'id_predio' => $pago->predio->id_predio,
                'clave_catastral' => $pago->predio->Clave_predial,
                'domicilio' => $pago->predio->ubicacion_completa,
                'calle' => $pago->predio->calle?->CALLE,
                'colonia' => $pago->predio->colonia?->COLONIA,
                'codigo_postal' => $pago->predio->codigo_postal,
            ] : null,

            'conceptos' => $pago->cuentasPagos->map(fn ($c) => [
                'cuenta_codigo' => $c->cuenta?->cuenta ?? $c->cuenta?->indetec,
                'cuenta_indetec' => $c->cuenta?->indetec,
                'cuenta_descripcion' => $c->cuenta?->descripcion ?? $c->concepto,
                'concepto' => $c->concepto,
                'cantidad' => $c->cantidad,
                'monto' => (float) $c->monto,
            ])->values(),

            'formas_pago' => $pago->formasPagosCada->map(fn ($fp) => [
                'forma_pago' => $fp->formaPago?->{'Descripción'},
                'clave_sat' => $fp->formaPago?->c_FormaPago,
                'monto' => (float) $fp->monto,
            ])->values(),

            'caja' => $pago->historialCaja ? [
                'nombre' => $pago->historialCaja->caja?->nombre,
                'ubicacion' => $pago->historialCaja->caja?->ubicacion,
                'cajero' => $pago->historialCaja->cajero?->usuario?->name,
            ] : null,

            'usuario_registro' => $pago->user?->name,
        ];
    }
}
