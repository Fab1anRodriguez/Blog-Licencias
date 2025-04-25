document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formulario_registro');
    const inputs = {
        doc_usu: /^\d{8,12}$/,
        nom_usu: /^[a-zA-ZÀ-ÿ\s]{3,40}$/,
        correo: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/,
        password: /^.{4,12}$/,
    };

    const validarFormulario = (e) => {
        switch (e.target.name) {
            case "doc_usu":
                validarCampo(e.target, 'doc_usu');
                break;
            case "nom_usu":
                validarCampo(e.target, 'nom_usu');
                break;
            case "correo":
                validarCampo(e.target, 'correo');
                break;
            case "password":
                validarCampo(e.target, 'password');
                validarPassword2();
                break;
            case "password2":
                validarPassword2();
                break;
        }
    };

    const validarCampo = (input, campo) => {
        const grupo = document.getElementById(`grupo__${campo}`);
        if(inputs[campo].test(input.value)) {
            grupo.classList.remove('formulario__grupo-incorrecto');
            grupo.classList.add('formulario__grupo-correcto');
            grupo.querySelector('.formulario__input-error').style.display = 'none';
            return true;
        } else {
            grupo.classList.add('formulario__grupo-incorrecto');
            grupo.classList.remove('formulario__grupo-correcto');
            grupo.querySelector('.formulario__input-error').style.display = 'block';
            return false;
        }
    };

    const validarPassword2 = () => {
        const password1 = document.getElementById('password');
        const password2 = document.getElementById('password2');
        const grupo = document.getElementById('grupo__password2');

        if(password1.value === password2.value && password1.value !== '') {
            grupo.classList.remove('formulario__grupo-incorrecto');
            grupo.classList.add('formulario__grupo-correcto');
            grupo.querySelector('.formulario__input-error').style.display = 'none';
            return true;
        } else {
            grupo.classList.add('formulario__grupo-incorrecto');
            grupo.classList.remove('formulario__grupo-correcto');
            grupo.querySelector('.formulario__input-error').style.display = 'block';
            return false;
        }
    };

    //ver foto antes de subir
    //document.getElementById('foto').addEventListener('change', function(e) {
    //    const reader = new FileReader();
    //    reader.onload = function(e) {
    //        document.getElementById('foto-preview').src = e.target.result;
    //    }
    //    reader.readAsDataURL(this.files[0]);
    //});

    // Event listeners para todos los campos
    const campos = ['doc_usu', 'nom_usu', 'correo', 'password', 'password2'];
    campos.forEach(campo => {
        const input = document.getElementById(campo);
        input.addEventListener('keyup', validarFormulario);
        input.addEventListener('blur', validarFormulario);
    });

    // validacion del formulario al enviar
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // detener el envio normal
        let errores = [];
        
        // validar todos los campos
        campos.forEach(campo => {
            const input = document.getElementById(campo);
            
            // validar si esta vacio
            if(input.value.trim() === '') {
                errores.push(`${campo === 'doc_usu' ? 'Documento de identidad' :
                            campo === 'nom_usu' ? 'Nombre completo' :
                            campo === 'correo' ? 'Correo electronico' :
                            campo === 'password' ? 'Contraseña' :
                            'Confirmacion de contraseña'} (campo vacio)`);
            } 
            // validar formato si no esta vacio
            else if(campo !== 'password2') {
                if(!validarCampo(input, campo)) {
                    errores.push(`${campo === 'doc_usu' ? 'Documento de identidad' :
                                campo === 'nom_usu' ? 'Nombre completo' :
                                campo === 'correo' ? 'Correo electronico' :
                                'Contraseña'} (formato invalido)`);
                }
            }
        });

        // validar confirmacion de contraseña
        if(document.getElementById('password2').value.trim() !== '' && !validarPassword2()) {
            errores.push('Las contraseñas no coinciden');
        }

        if(errores.length > 0) {
            // mostrar errores
            alert('Por favor, corrija los siguientes errores:\n- ' + errores.join('\n- '));
        } else {
            // agregar un console.log para depurar
            console.log('Enviando formulario...');
            
            // desactivar el boton para evitar doble envio
            const submitButton = document.querySelector('button[type="submit"]');
            if (submitButton) submitButton.disabled = true;
            
            // enviar el formulario
            this.submit();
        }
    });
});