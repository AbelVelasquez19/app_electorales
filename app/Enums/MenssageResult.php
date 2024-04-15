<?php
namespace App\Enums;
class MensageResult{
    const MESSAGE_ADD = 'La informacíon se guardó correctamente';
    const MESSAGE_ERROR = 'La informacíon no se guardó';
    const MESSAGE_UPDATE = 'La informacíon se actualizó correctamente';
    const MESSAGE_DELETE = 'La informacíon se eliminó correctamente';
    const MESSAGE_SUCCESS_CARGAR_IMAGEN = 'El archivo se cargo Correctamente';
    const MESSAGE_ERROR_CARGAR_IMAGEN = 'Error al momento de cargar archivo';
    const MESSAGE_ERROR_CARGAR_IMAGEN_UPDATE = 'Error, es obligatorio cargar';
    const MESSAGE_ERROR_LIST = 'Registro no encontrado';
}