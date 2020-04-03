<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/foo', function () {
    return array('esto es un prueba ','de laravel');
}); 


Route::group(['prefix'=>'login','middleware' =>['webendpoints']],function(){
	Route::post('control/encrypt','Login\LoginController@hashEnc');
	Route::post('control/descrypt','Login\LoginController@hashDes');
	Route::post('control/valida','Login\LoginController@validateUser');
	Route::post('control/validapwd','Login\LoginController@validatePwd');
	Route::post('control/obtMenus','Login\LoginController@obtMenus');
});

/*
Route::get('/foo', function () {
    return array('esto es un prueba ','de laravel');
}); 
*/
/**
 * Rutas para los Catálogos
 */
Route::group(['prefix' => 'catalogos', 'middleware' => ['webendpoints']], function () {
	// Deshabilitar metodo GET

	Route::get('{ruta?}/{ruta2?}/{ruta3?}', function () { abort(404); });
	// Deshabilitar metodos PUT, DELETE, OPTIONS y PATCH
	Route::match(['put', 'delete', 'options', 'patch'], '{ruta?}/{ruta2?}/{ruta3?}', function () { abort(500); });
  
	// Rutas para Coordinadores
	/*Route::get('/foo', function () {
		return 'Hello World';
	});*/
	Route::post('coordinadores/all', 'Catalogos\CoordinadoresController@index');
	Route::post('coordinadores/combo', 'Catalogos\CoordinadoresController@show');
	Route::post('coordinadores/save', 'Catalogos\CoordinadoresController@store');
	Route::post('coordinadores/del', 'Catalogos\CoordinadoresController@destroy');

	// Rutas para Técnicos
	Route::post('tecnicos/all', 'Catalogos\TecnicosController@index');
	Route::post('tecnicos/combo', 'Catalogos\TecnicosController@show');
	Route::post('tecnicos/save', 'Catalogos\TecnicosController@store');
	Route::post('tecnicos/del', 'Catalogos\TecnicosController@destroy');

	// Rutas para Tipos de Estatus
	Route::post('tiposEstatus/all', 'Catalogos\TiposEstatusController@index');
	Route::post('tiposEstatus/combo', 'Catalogos\TiposEstatusController@show');
	Route::post('tiposEstatus/save', 'Catalogos\TiposEstatusController@store');
	Route::post('tiposEstatus/del', 'Catalogos\TiposEstatusController@destroy');

	// Rutas para Estatus
	Route::post('estatus/all', 'Catalogos\EstatusController@index');
	Route::post('estatus/combo', 'Catalogos\EstatusController@show');
	Route::post('estatus/save', 'Catalogos\EstatusController@store');
	Route::post('estatus/del', 'Catalogos\EstatusController@destroy');

	// Rutas para Gastos
	Route::post('gastos/all', 'Catalogos\GastosController@index');
	Route::post('gastos/combo', 'Catalogos\GastosController@show');
	Route::post('gastos/save', 'Catalogos\GastosController@store');
	Route::post('gastos/del', 'Catalogos\GastosController@destroy');

	// Rutas para Tipos de Concepto de Gastos
	Route::post('gastosDetalle/all', 'Catalogos\GastosDetalleController@index');
	Route::post('gastosDetalle/combo', 'Catalogos\GastosDetalleController@show');
	Route::post('gastosDetalle/save', 'Catalogos\GastosDetalleController@store');
	Route::post('gastosDetalle/del', 'Catalogos\GastosDetalleController@destroy');

	// Rutas para Tipos de Documentos
	Route::post('tiposDocumentos/all', 'Catalogos\TiposDocumentosController@index');
	Route::post('tiposDocumentos/save', 'Catalogos\TiposDocumentosController@store');
	Route::post('tiposDocumentos/del', 'Catalogos\TiposDocumentosController@destroy');

	// Rutas para Tipos de Servicios
	Route::post('tiposServicios/all', 'Catalogos\TiposServiciosController@index');
	Route::post('tiposServicios/combo', 'Catalogos\TiposServiciosController@show');
	Route::post('tiposServicios/save', 'Catalogos\TiposServiciosController@store');
	Route::post('tiposServicios/del', 'Catalogos\TiposServiciosController@destroy');

	// Rutas para Zonas
	Route::post('zonas/all', 'Catalogos\ZonasController@index');
	Route::post('zonas/combo', 'Catalogos\ZonasController@show');
	Route::post('zonas/save', 'Catalogos\ZonasController@store');
	Route::post('zonas/del', 'Catalogos\ZonasController@destroy');
	Route::post('zonas/estados', 'Catalogos\ZonasController@estados');
	Route::post('zonas/tecnicos', 'Catalogos\ZonasController@tecnicos');
	Route::post('zonas/tecnicos/save', 'Catalogos\ZonasController@ZonaTecnicos');
	Route::post('zonas/estados/save', 'Catalogos\ZonasController@ZonaEstados');
	Route::post('zonas/estados/obtEstados', 'Catalogos\ZonasController@obtEstados');

	// Rutas para Vias
	Route::post('vias/all', 'Catalogos\ViasController@index');
	Route::post('vias/combo', 'Catalogos\ZonasController@show');
	Route::post('vias/save', 'Catalogos\ViasController@store');
	Route::post('vias/del', 'Catalogos\ViasController@destroy');

	// Rutas para Motivos
	Route::post('motivos/all', 'Catalogos\MotivosController@index');
	Route::post('motivos/combo', 'Catalogos\MotivosController@show');
	Route::post('motivos/save', 'Catalogos\MotivosController@store');
	Route::post('motivos/del', 'Catalogos\MotivosController@destroy');

	// Rutas para Medios
	Route::post('medios/all', 'Catalogos\MediosController@index');
	Route::post('medios/combo', 'Catalogos\MediosController@show');
	Route::post('medios/save', 'Catalogos\MediosController@store');
	Route::post('medios/del', 'Catalogos\MediosController@destroy');

	// Rutas para Equipos
	Route::post('equipos/all', 'Catalogos\EquiposController@index');
	Route::post('equipos/combo', 'Catalogos\EquiposController@show');
	Route::post('equipos/save', 'Catalogos\EquiposController@store');
	Route::post('equipos/caracteristicas/obtCaracts', 'Catalogos\EquiposController@obtCaracts');
	Route::post('equipos/caracteristicas/save', 'Catalogos\EquiposController@saveCaracts');
	Route::post('equipos/del', 'Catalogos\EquiposController@destroy');

	// Rutas para Formato Actas
	Route::post('formatoActas/all', 'Catalogos\FormatoActasController@index');
	Route::post('formatoActas/combo', 'Catalogos\FormatoActasController@show');
	Route::post('formatoActas/save', 'Catalogos\FormatoActasController@store');
	Route::post('formatoActas/del', 'Catalogos\FormatoActasController@destroy');

});

/**
 * Rutas para consultar información externa al sistema
 */
Route::group(['prefix' => 'generales', 'middleware' => ['webendpoints']], function () {
	// Deshabilitar metodo GET
	Route::get('{ruta?}/{ruta2?}}', function () { abort(404); });
	// Deshabilitar metodos PUT, DELETE, OPTIONS y PATCH
	Route::match(['put', 'delete', 'options', 'patch'], '{ruta?}/{ruta2?}}', function () { abort(500); });

	Route::post('clientes/combo', 'General\CatalogosController@clientes');
	Route::post('sucursales/combo', 'General\CatalogosController@sucursales');
	Route::post('estados/combo', 'General\CatalogosController@estados');
	Route::post('ciudades/combo', 'General\CatalogosController@ciudades');
	Route::post('servicios/combo', 'General\CatalogosController@servicios');
	Route::post('refacciones/combo', 'General\CatalogosController@refacciones');
	Route::post('serviciosRef/combo', 'General\CatalogosController@serviciosRef');
	Route::post('obtServiciosRef/combo', 'General\CatalogosController@obtServiciosRef');
});

/**
 * Rutas para Cotizaciones
 */
Route::group(['prefix' => 'cotizaciones', 'middleware' => ['webendpoints']], function () {
	// Deshabilitar metodo GET
	Route::get('{ruta?}/{ruta2?}/{ruta3?}', function () { abort(404); });
	// Deshabilitar metodos PUT, DELETE, OPTIONS y PATCH
	Route::match(['put', 'delete', 'options', 'patch'], '{ruta?}/{ruta2?}/{ruta3?}', function () { abort(500); });

	// Nuevas Cotizaciones
	Route::post('obtener/sucursal', 'Sistema\CotizacionesController@obtsucursal');
	Route::post('obtener/costosCliente', 'Sistema\CotizacionesController@obtCostosCliente');
	Route::post('obtener/cliente/servicios', 'Sistema\CotizacionesController@obtservicios');

	Route::post('nueva/save', 'Sistema\CotizacionesController@cotizacion');
	Route::post('nuevos/servicios', 'Sistema\CotizacionesController@servicios');
	Route::post('nuevos/traslados', 'Sistema\CotizacionesController@traslados');
	Route::post('nuevos/viaticos', 'Sistema\CotizacionesController@viaticos');

	// Detalle de la sucursal seleccionada
	Route::post('sucursal/detalle', 'Sistema\CotizacionesController@sucursalDetalle');

	// Seguimiento
	Route::post('seguimiento/all', 'Sistema\CotizacionesController@cotizaciones');
	Route::post('seguimiento/modestatus', 'Sistema\CotizacionesController@modestatus');
	Route::post('seguimiento/modmotivo', 'Sistema\CotizacionesController@modmotivo');

	// Seguimiento: Obtener el detalle de la cotizacion: Productos/Servicios - Traslados - Viáticos
	Route::post('seguimiento/detalleListas', 'Sistema\CotizacionesController@cotizaciones');

});

/**
 * Rutas para Mesa de Control
 */
Route::group(['prefix' => 'mesaControl', 'middleware' => ['webendpoints']], function () {
	// Deshabilitar metodo GET
	/*
	Route::get('{ruta?}/{ruta2?}/{ruta3?}', function () { abort(404); });
	// Deshabilitar metodos PUT, DELETE, OPTIONS y PATCH
	Route::match(['put', 'delete', 'options', 'patch'], '{ruta?}/{ruta2?}/{ruta3?}', function () { abort(500); });
    */
	Route::post('coordinador/combo', 'Sistema\MesaControlController@coordinador');
	Route::post('servicios/combo', 'Sistema\MesaControlController@servicios');
	Route::post('sucursal/obtFolios', 'Sistema\MesaControlController@obtFolioSucursal');
	Route::post('obtener/cliente/servicios', 'Sistema\MesaControlController@obtservicios');
	Route::post('nuevo/save', 'Sistema\MesaControlController@nuevofolio');
	Route::post('servicios/save', 'Sistema\MesaControlController@serviciosfolio');
	Route::post('nuevo/saveservices','Sistema\MesaControlController@saveServices');
	Route::post('nuevo/saveXls','Sistema\MesaControlController@saveFolio');
	// Seguimiento
	Route::post('seguimiento/all', 'Sistema\MesaControlController@obtFolios');
	Route::post('seguimiento/serviciosFolio', 'Sistema\MesaControlController@obtProductosFolio');
	Route::post('seguimiento/tecnicosFolio', 'Sistema\MesaControlController@obtTecnicosFolio');
	Route::post('seguimiento/tecnicosFolio/save', 'Sistema\MesaControlController@saveTecnicosFolio');
	Route::post('seguimiento/tecnicosZonaFolio', 'Sistema\MesaControlController@obtTecnicosZonaFolio');
	Route::post('seguimiento/documentosFolio', 'Sistema\MesaControlController@obtDocumentosFolio');
	Route::post('seguimiento/tiposDocumentos', 'Sistema\MesaControlController@obtTiposDocumentos');
	Route::post('seguimiento/documentos/save', 'Sistema\MesaControlController@saveDocumentofolio');
	Route::post('seguimiento/tecnicoFolio/detalle', 'Sistema\MesaControlController@obtDetalleTecnico');
	Route::post('seguimiento/tecnicoFolio/detalleInventario', 'Sistema\MesaControlController@obtDetalleInventarioTecnico');
	Route::post('seguimiento/tecnicoFolio/detalleRefacciones', 'Sistema\MesaControlController@obtDetalleRefaccionesTecnico');
	Route::post('seguimiento/tecnicoFolio/obtRefaccionesTecnico', 'Sistema\MesaControlController@obtRefaccionesTecnico');
	Route::post('seguimiento/tecnicoFolio/RefaccionesSave', 'Sistema\MesaControlController@insDetalleRefaccionesTecnico');
	Route::post('seguimiento/tecnicoFolio/RefaccionesDel', 'Sistema\MesaControlController@eliRefaServsFolioTecnico');
	Route::post('seguimiento/tecnicoFolio/edoCuenta','Sistema\MesaControlController@obtEdoCuenta');
	Route::post('seguimiento/sucursalFolio/detalle', 'Sistema\MesaControlController@obtDetalleSucursal');
	Route::post('seguimiento/documentos/download', 'Sistema\MesaControlController@downDocumentoFolio');
	Route::post('seguimiento/documentos/downloadValidar', 'Sistema\MesaControlController@validarDocumentoFolio');
	Route::post('seguimiento/documentos/downloadZip','Sistema\MesaControlController@downloadZip');
	Route::post('seguimiento/estatusMotivoFolio', 'Sistema\MesaControlController@obtMotivoEstatusFolio');
	Route::post('seguimiento/estatusMotivoFolio/save', 'Sistema\MesaControlController@saveMotivoEstatusFolio');
	Route::post('seguimiento/infoGeneralFolio/save', 'Sistema\MesaControlController@saveInfoGeneralFolio');
	Route::post('seguimiento/actulizacion/save', 'Sistema\MesaControlController@saveActualizacioFolio');
	Route::post('seguimiento/actulizacion/obtActualizaciones', 'Sistema\MesaControlController@obtActualizacioesFolio');
	Route::post('seguimiento/actulizacion/obtActualizacionesNum', 'Sistema\MesaControlController@obtNumActualizacioesFolio');
	Route::post('seguimiento/obtActividades', 'Sistema\MesaControlController@obtActividades');
	Route::post('seguimiento/obtActasTecnico', 'Sistema\MesaControlController@obtActasTecnico');
	Route::post('seguimiento/actasFolio/save', 'Sistema\OperacionesController@saveActasFolio');
	Route::post('seguimiento/obtFolios','Sistema\MesaControlController@obtContFol');
	Route::post('seguimiento/obtCat','Sistema\MesaControlController@obtContCot');
	
});

/**
 * Rutas para Operaciones
 */
Route::group(['prefix' => 'operaciones', 'middleware' => ['webendpoints']], function () {
	// Deshabilitar metodo GET
	Route::get('{ruta?}/{ruta2?}/{ruta3?}', function () { abort(404); });
	// Deshabilitar metodos PUT, DELETE, OPTIONS y PATCH
	Route::match(['put', 'delete', 'options', 'patch'], '{ruta?}/{ruta2?}/{ruta3?}', function () { abort(500); });

	// Mis Servicios
	Route::post('misServicios/actasFolio/save', 'Sistema\OperacionesController@saveActasFolio');
	Route::post('servicios/all', 'Sistema\OperacionesController@obtServicios');
	Route::post('servicios/save', 'Sistema\OperacionesController@servicefilter');
	// Seguimiento Servicios
	Route::post('seguimiento/all', 'Sistema\OperacionesController@obtServicios');
	// Mis Técnicos
	Route::post('misTecnicos/all', 'Sistema\OperacionesController@obtTecnicos');
	Route::post('misTecnicos/actas/save', 'Sistema\OperacionesController@saveActasTecnico');
	Route::post('misTecnicos/actasFolio', 'Sistema\OperacionesController@obtActasFolioTecnico');
	//Route::post('misTecnicos/estadoCuenta', 'Sistema\OperacionesController@obtEstadoCuentaTecnico');
	Route::post('advertencias/acta', 'Sistema\OperacionesController@obtAdvertenciasActa');
	Route::post('misTecnicos/estadoCuenta','Sistema\OperacionesController@obtEstadosCuenta');
	// Armado de Actas
	Route::post('armadoActas/all', 'Sistema\OperacionesController@obtActas');
	Route::post('armadoActas/enviar', 'Sistema\OperacionesController@enviarActas');
	Route::post('armadoActas/enviarCorreo', 'Sistema\OperacionesController@enviarCorreo');
	// Mi Calendario
	Route::post('miCalendario/servicios', 'Sistema\OperacionesController@obtServsCalendario');
	// Calendario General
	Route::post('calendario/serviciosGral', 'Sistema\OperacionesController@obtServsCalendario');
	
});

/**
 * Rutas para Administración
 */
Route::group(['prefix' => 'administracion', 'middleware' => ['webendpoints']], function () {
	// Deshabilitar metodo GET
	Route::get('{ruta?}/{ruta2?}/{ruta3?}', function () { abort(404); });
	// Deshabilitar metodos PUT, DELETE, OPTIONS y PATCH
	Route::match(['put', 'delete', 'options', 'patch'], '{ruta?}/{ruta2?}/{ruta3?}', function () { abort(500); });

	// Administración de Actas
	Route::post('adminActas/all', 'Sistema\AdministracionController@obtSolicitudesAprobadas');
	Route::post('adminActas/detalleActas/all', 'Sistema\AdministracionController@obtDetalleActas');
	Route::post('adminActas/detalleActas/motivos', 'Sistema\AdministracionController@obtMotivosActas');
	Route::post('adminActas/validarActa/save', 'Sistema\AdministracionController@saveValidarActas');

	// Autorizar Solicitudes de Actas
	Route::post('solicitudesActas/all', 'Sistema\AdministracionController@obtSolicitudesAutorizar');
	Route::post('aprobarSolicitud/save', 'Sistema\AdministracionController@saveAprobarSolicitud');
	Route::post('motivoSolicitud/save', 'Sistema\AdministracionController@saveSolicitudMotivo');
	Route::post('advertencias/all', 'Sistema\AdministracionController@obtAdvertenciasActa');

	// Recepción de Actas Operacionales
	Route::post('actasEnviadas/all', 'Sistema\AdministracionController@obtActasEnviadas');
	Route::post('actasEnviadas/detalle', 'Sistema\AdministracionController@obtDetalleActasEnviadas');
	Route::post('actasRecepcion/confirmar', 'Sistema\AdministracionController@saveConfirmarActas');

	// Seguimiento Folios de Servicio
	Route::post('seguimientoFolios/all', 'Sistema\AdministracionController@obtSeguimFolios');
	Route::post('seguimientoFolios/reportar/save', 'Sistema\AdministracionController@saveReporteServicios');
});

/**
 * Rutas para Gastos
 */
Route::group(['prefix' => 'gastos', 'middleware' => ['webendpoints']], function () {
	// Deshabilitar metodo GET
	Route::get('{ruta?}/{ruta2?}/{ruta3?}', function () { abort(404); });
	// Deshabilitar metodos PUT, DELETE, OPTIONS y PATCH
	Route::match(['put', 'delete', 'options', 'patch'], '{ruta?}/{ruta2?}/{ruta3?}', function () { abort(500); });

	// Tipo de Gasto General
	Route::post('tipoGasto/combo', 'Sistema\GastosController@obtTiposGastoCombo');
	
	// Obtener la lista de Empleados
	Route::post('empleados/combo', 'Sistema\GastosController@obtEmpleadosCombo');
	// Lista de servicios (OdeS) del empleado seleccionado
	Route::post('empleado/servicios', 'Sistema\GastosController@obtServiciosEmpleado');
	// Lista de productos asociados al empleado seleccionado
	Route::post('empleado/productos', 'Sistema\GastosController@obtProductosEmpleado');
	// Adeudo Empleado
	Route::post('empleado/adeudo','Sistema\GastosController@obtAdeudoEmpleado');
	// Solicitar informacion del estado de cuenta Regiosis
	Route::post('empleados/solInfo','Sistema\GastosController@solEdoCuenta');
	// Obtener el estado de cuenta del empleado
	Route::post('empleado/edoCuenta','Sistema\GastosController@obtEdoCuenta');
	
	// Deducibles para los Conceptos de Gastos
	Route::post('tipoGasto/concepto/combo', 'Sistema\GastosController@obtTipoGastoConcepto');
	Route::post('conceptosGasto/concepto/combo', 'Sistema\GastosController@obtConceptosGastoConcepto');
	
	// Guardar Gasto
	Route::post('nuevoGasto/save', 'Sistema\GastosController@saveGastoGeneral');
	Route::post('nuevoGasto/OdeS/save', 'Sistema\GastosController@saveGastoServicios');
	Route::post('nuevoGasto/productos/save', 'Sistema\GastosController@saveGastoProductos');
	Route::post('nuevoGasto/conceptos/save', 'Sistema\GastosController@saveGastoConcepto');
	Route::post('nuevoGasto/conceptosDetalle/save', 'Sistema\GastosController@saveGastoConceptoDetalle');

	// Segiomiento de Gastos
	Route::post('seguimiento/all', 'Sistema\GastosController@obtGastoGeneral');
	Route::post('seguimiento/estatus/combo', 'Sistema\GastosController@obtEstatusGastos');
	Route::post('seguimiento/servicios', 'Sistema\GastosController@obtServiciosSegGastos');
	Route::post('seguimiento/productos', 'Sistema\GastosController@obtProductosSegGastos');
	Route::post('seguimiento/conceptos', 'Sistema\GastosController@obtConceptosSegGastos');
	Route::post('seguimiento/conceptos/detalle', 'Sistema\GastosController@obtConceptosDetSegGastos');
	// Estatus Gasto
	Route::post('seguimiento/estatusGasto/save', 'Sistema\GastosController@saveEstatusGasto');
	Route::post('seguimiento/motivoGasto/combo', 'Sistema\GastosController@obtMotivoEstatus');
	Route::post('seguimiento/motivoGasto/save', 'Sistema\GastosController@saveMotivoEstatus');
	// Reportar Gasto
	Route::post('seguimiento/reportarGasto/save', 'Sistema\GastosController@saveReportarGasto');
	
});


/**
 * Rutas para consultar información externa al sistema
 */
Route::group(['prefix' => 'movil', 'middleware' => ['mobilendpoints']], function () {
	// Deshabilitar metodo GET
	//Route::get('{ruta?}/{ruta2?}}', function () { abort(404); });
	// Deshabilitar metodos PUT, DELETE, OPTIONS y PATCH
	Route::match(['put', 'delete', 'options', 'patch'], '{ruta?}/{ruta2?}}', function () { abort(500); });

	// Mis Servicios
	Route::post('servicios/all', 'Movil\MisServiciosController@servicios');
	Route::get('servicios/all', 'Movil\MisServiciosController@servicios');
	Route::get('servicios/detalle', 'Movil\MisServiciosController@serviciosDetalle');

	// Estado de Cuenta
	Route::post('estadoCuenta/all', 'Movil\EstadoCuentaController@estadoCuenta');
	Route::get('estadoCuenta/all', 'Movil\EstadoCuentaController@estadoCuenta');

	// Mi Inventario
	Route::post('inventario/all', 'Movil\MiInventarioController@inventario');
	Route::get('inventario/all', 'Movil\MiInventarioController@inventario');

	// Estatus
	Route::post('estatus/combo', 'Catalogos\EstatusController@show');
	Route::get('estatus/combo', 'Catalogos\EstatusController@show');

	// Actualizar Ubicación Empleado
	Route::post('actualizar/ubicacion', 'Movil\MisServiciosController@saveubicacion');
});


/**
 * Ruta para retornar token
 */
/*Route::prefix('info')->group(function () {
	// Retornar token
	Route::get('verify/Authentication/Token', function() { return csrf_token(); });

});*/
