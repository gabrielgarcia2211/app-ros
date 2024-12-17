<script>
const readStatus = {};

// Funcion para obtener los datos del error de las respuesta http
readStatus.getDataError = (error = []) => {
    let response = {};
    try {
        response = [error][0].response;
    } catch (error) {
        response = {};
    }
    return response;
}

readStatus.validateForm = (data) => {
    const errors = data.errors;
    if (errors) {
        let messages = '';
        Object.values(errors).forEach(errorList => {
            errorList.forEach(error => {
                messages += error + '<br>';
            });
        });
        return messages;
    }
}

export default {
    methods: {
        $readStatusHttp(error = []) {
            const response = readStatus.getDataError(error);

            switch (response.status) {
                case 400:
                    this.$alertDanger('Solicitud Incorrecta', response.data.message);
                    break;
                case 401:
                    this.$alertDanger('Tu sesión expiró', "");
                    setTimeout(() => window.location.href = "/", 2000);
                    break;
                case 402:
                    this.$alertDanger('Solicitud Incorrecta');
                    break;
                case 403:
                    this.$alertDanger('Proceso denegado', 'No tienes permiso para realizar este proceso');
                    break;
                case 404:
                    this.$alertDanger('Solicitud Incorrecta');
                    break;
                case 406:
                    this.$alertDanger('Solicitud Incorrecta');
                    break;
                case 409:
                    this.$alertDanger('Proceso no autorizado', 'El proceso no fue autorizado, intente con otro tipo de proceso');
                    break;
                case 419:
                    this.$alertDanger('Tu sesión expiró', "");
                    setTimeout(() => window.location.href = "/", 2000);
                    break;
                case 422:
                    this.$alertDanger('Error de formulario', readStatus.validateForm(response.data));
                    break;
                case 423:
                    this.$alertDanger('Limite Excedido');
                    break;
                case 500:
                    this.$alertDanger('Solicitud Incorrecta', 'Ocurrió un error al realizar el proceso');
                    break;
                default:
                    this.$alertDanger('Solicitud Incorrecta', 'Ocurrió un error inesperado');
            }
        }
    }
}
</script>