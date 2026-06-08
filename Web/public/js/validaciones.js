/**
 * Script de validación para los formularios de productos.
 * Revisa que los datos estén bien antes de enviarlos a PHP.
 */

// Esperamos a que la página esté completamente lista
document.addEventListener("DOMContentLoaded", function() {
    
    // Buscamos el formulario. Usamos el mismo para "Crear" y "Editar"
    const formulario = document.getElementById("formProducto");
    
    // Solo seguimos si el formulario existe en esta pantalla
    if (formulario) {
        
        const inputNombre = document.getElementById("nombre");
        const inputPrecio = document.getElementById("precio");

        // 1. Revisar el NOMBRE cuando el usuario pincha fuera de la casilla
        inputNombre.addEventListener("blur", function() {
            // Solo permitimos letras, números y espacios (ejemplo: "Mesa de roble 2")
            const regexLetras = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s]+$/;
            
            // Quitamos los espacios vacíos que se hayan colado al principio o al final
            const valorLimpio = inputNombre.value.trim();

            // Si está vacío o tiene símbolos no permitidos, mostramos el error en rojo
            if (valorLimpio === "" || !regexLetras.test(valorLimpio)) {
                inputNombre.classList.add("is-invalid");
            } else {
                // Si todo está bien, quitamos el error y lo ponemos en mayúsculas
                inputNombre.classList.remove("is-invalid");
                inputNombre.value = valorLimpio.toUpperCase();
            }
        });

        // 2. Revisar el PRECIO cuando el usuario pincha fuera de la casilla
        inputPrecio.addEventListener("blur", function() {
            // Convertimos el texto a número con decimales
            const valorPrecio = parseFloat(inputPrecio.value);

            // Tiene que ser un número válido y mayor a cero
            if (inputPrecio.value === "" || valorPrecio <= 0 || isNaN(valorPrecio)) {
                inputPrecio.classList.add("is-invalid");
            } else {
                inputPrecio.classList.remove("is-invalid");
            }
        });

        // 3. Revisión final al darle al botón de GUARDAR
        formulario.addEventListener("submit", function(evento) {
            // Hacemos una comprobación rápida por si dejaron los campos en blanco
            if (inputNombre.value.trim() === "") inputNombre.classList.add("is-invalid");
            if (inputPrecio.value === "") inputPrecio.classList.add("is-invalid");

            // Miramos si hay alguna casilla marcada en rojo (con error)
            const camposConErrores = formulario.querySelectorAll(".is-invalid");

            if (camposConErrores.length > 0) {
                // Frenamos el envío para que el formulario no llegue roto a la base de datos
                evento.preventDefault();
                alert("Por favor, corrige los campos marcados en rojo.");
            } else {
                // Si todo está correcto, preguntamos para estar seguros
                const proceder = confirm("¿Estás seguro de que deseas guardar este producto?");
                if (!proceder) {
                    evento.preventDefault(); // Cancelamos la acción si el usuario se arrepiente
                }
            }
        });
        
    }
});