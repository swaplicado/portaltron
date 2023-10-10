<?php namespace App\Utils;

class FilesUtils {

    /**
     * Convierte un tamaño de archivo en su equivalente en bytes.
     * @param string $tamanio Tamaño del archivo con su unidad (ejemplo: 10 MB).
     * @return int|false El tamaño del archivo en bytes, o false en caso de error.
     */
    public static function convertirTamanioBytes($tamanio){
        $unidades = array('B', 'KB', 'MB', 'GB', 'TB');
        $posicion = array_search(substr($tamanio, -2), $unidades);
        if ($posicion === false) {
            return false; // Unidad de tamaño desconocida
        }
        $tamanio = trim(substr($tamanio, 0, -2));
        if (!is_numeric($tamanio)) {
            return false; // El tamaño no es un número
        }
        return $tamanio * pow(1024, $posicion);
    }

    /**
     * Funcion que valida el tipo de archivo recibido y regresa true o false
     * @param mixed $file el archivo a validar
     * @param string $filetype el tipo de archivo a validar, por ejemplo 'pdf', 'xml'...
     * @param int $fileSize el tamaño en bytes del archivo a validar
     */
    public static function validateFile($file, $fileType = null, $fileSize = null){
        $sizeBytes = FilesUtils::convertirTamanioBytes($fileSize);

        if ($file->isValid()) {
            // Validamos el tipo de archivo
            $aux = $file->getMimeType();
            if ($file->getMimeType() !== 'application/'.$fileType && $file->getMimeType() !== 'text/'.$fileType) {
                return [false, 'El archivo debe ser un '.$fileType];
            }
            // Validamos el tamaño del archivo en bytes
            $f = $file->getSize();
            if ($file->getSize() > $sizeBytes) {
                return [false, 'El archivo no debe pesar mas de '.$fileSize];
            }
            
            return [true, 'Ok'];
        }
    }
}