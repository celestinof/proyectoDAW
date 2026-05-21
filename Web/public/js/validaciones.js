//1. ESPERAR A QUE CARGUE LA WEB
// Escuchamos el evento 'DOMContentLoaded', que se activa cuando el HTML está listo
document.addEventListener("DOMContentLoaded", function() {
    console.log("JavaScript cargado"); 


//2. CAPTURAMOS LOS INPUTS
// Buscamos los elementos del DOM por su ID
    const formulario = document.getElementById("formCrearProducto");
    const inputNombre = document.getElementById("nombre");
    const inputPrecio = document.getElementById("precio");


//3. Cuando el usuario sale del campo nombre queremos comprobar que no esté vacío y sea solo letras     
// Escuchamos el evento 'blur' (perder el foco) en el input del nombre
    inputNombre.addEventListener("blur", function() {
    // Expresión regular: permite letras con acentos, ñ y espacios (la busqué por internet realmente).
    //A diferencia de la que usamos en la tarea, permite números (por ejemplo, llavero2)
    const regexLetras = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/;

    // .value.trim() quita los espacios en blanco que el usuario pueda poner sin querer al principio o final
    if (inputNombre.value.trim() === "" || !regexLetras.test(inputNombre.value)) {
            // Si está vacío o no son solo letras, añadimos la clase de Bootstrap para poner el borde rojo
        inputNombre.classList.add("is-invalid");
    } else {
            // Si es correcto, quitamos la clase de error por si acaso estaba puesta
        inputNombre.classList.remove("is-invalid");
            
            // Lo pasamos a MAYÚSCULAS
        inputNombre.value = inputNombre.value.toUpperCase();
    }
});

//Paso 4: Validar el Precio al pinchar fuera (que sea mayor a 0)
    inputPrecio.addEventListener("blur", function() {
        // Convertimos el texto a número decimal con parseFloat
        const valorPrecio = parseFloat(inputPrecio.value);

        if (inputPrecio.value === "" || valorPrecio <= 0 || isNaN(valorPrecio)) {
            inputPrecio.classList.add("is-invalid");
        } else {
            inputPrecio.classList.remove("is-invalid");
        }
    });

//Paso 5: Controlar el botón de enviar (submit)
//Por último, si el usuario hace clic en el botón de "Guardar", tenemos que revisar de golpe que todo esté correcto.
// Si hay algún campo con la clase is-invalid o vacío, detendremos el envío usando evento.preventDefault().
formulario.addEventListener("submit", function(evento) {
        // Comprobación rápida: si alguno está vacío, le metemos el error directamente
        if (inputNombre.value.trim() === "") inputNombre.classList.add("is-invalid");
        if (inputPrecio.value === "") inputPrecio.classList.add("is-invalid");

        // Buscamos si hay algún elemento en el formulario que tenga la clase de error 'is-invalid'
        const camposConErrores = formulario.querySelectorAll(".is-invalid");

        if (camposConErrores.length > 0) {
            // IMPORTANTE por que Evita que el formulario se envíe a PHP
            evento.preventDefault();
            alert("Por favor, corrige los campos en rojo antes de enviar.");
        } else {
            // Si todo está bien, pedimos confirmación
            const proceder = confirm("¿Estás seguro de que deseas guardar este producto?");
            if (!proceder) {
                evento.preventDefault(); // Si dice que no, cancelamos
            }
        }
    });
})