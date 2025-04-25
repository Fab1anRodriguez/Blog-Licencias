document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formulario_login');
    const inputs = {
        doc_usu: /^\d{8,12}$/,
        password: /^.{1,}$/
    };

    const validarFormulario = (e) => {
        switch (e.target.name) {
            case "doc_usu":
                validarCampo(e.target, 'doc_usu');
                break;
            case "password":
                validarCampo(e.target, 'password');
                break;
        }
    };

    const validarCampo = (input, campo) => {
        const grupo = document.getElementById(`grupo__${campo}`);
        if(inputs[campo].test(input.value)) {
            grupo.classList.remove('formulario__grupo-incorrecto');
            grupo.classList.add('formulario__grupo-correcto');
            grupo.querySelector('.formulario__input-error').style.display = 'none';
        } else {
            grupo.classList.add('formulario__grupo-incorrecto');
            grupo.classList.remove('formulario__grupo-correcto');
            grupo.querySelector('.formulario__input-error').style.display = 'block';
        }
    };

    // Event listeners
    const campos = ['doc_usu', 'password'];
    campos.forEach(campo => {
        const input = document.getElementById(campo);
        input.addEventListener('keyup', validarFormulario);
        input.addEventListener('blur', validarFormulario);
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const allValid = campos.every(campo => {
            const input = document.getElementById(campo);
            return inputs[campo].test(input.value);
        });

        if(allValid) {
            this.submit();
        }
    });
});