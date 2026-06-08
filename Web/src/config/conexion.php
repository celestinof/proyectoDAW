<?php
namespace App\Config; // Usamos el namespace para que el Autoloader de Composer lo pille automático y nos ahorramos los require.

// Importamos las clases globales de PDO. Si no hacemos esto, PHP intentará buscar PDO dentro de la carpeta Config y dará error Fatal.
use PDO;
use PDOException;

class Conexion {

    // Variables de configuración. 
    // OJO: Estos son los datos para el desarrollo en local (XAMPP). 
    // En el despliegue final en el servidor con Docker pasaron a ser $host="daw_artesanos_db", etc.
    private $host = "localhost";
    private $db_name = "artesanos_db";
    private $user = "root"; 
    private $password = ""; 

    // Función principal que llamaremos desde los Modelos cada vez que necesitemos hablar con la base de datos
    public function conectar() {

        // Montamos el string DSN (Data Source Name). Metemos el charset=utf8 directo para no tener problemas raros con eñes o tildes de la madera.
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";

        // Metemos la conexión en un try/catch. PDO por defecto es mudo ante los errores y te vuelves loco para depurar si falla la conexión.
        try {
            $conexion = new PDO($dsn, $this->user, $this->password);

            // Le decimos a PDO que sea estricto y lance excepciones si hay algún fallo en las consultas SQL
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Si llegamos aquí, todo ha ido bien
            return $conexion;

        } catch (PDOException $e) {
            // Si la conexión peta, matamos la ejecución del script (die) y mostramos el chivatazo del error
            die("Error fatal al conectar a la base de datos: " . $e->getMessage());
        }

    }
}