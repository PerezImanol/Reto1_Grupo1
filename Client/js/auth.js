const auth = {
    // Iniciar sesion enviando el nombre de usuario y la contraseña a la API
    async login(username, password) {
        const respuesta = await api.login({
            username: username,
            password: password
        });

        return respuesta;
    },

    // Comprueba si hay una sesion iniciada
    async obtenerSesion() {
        try {
            return await api.sesion();
        } catch (error) {
            return null;
        }
    },

    // Cierra la sesion y redirige al usuario al login
    async logout() {
        await api.logout();
        window.location.href = "login.html";
    },

    // Comprueba si hay una sesion iniciada y si no la hay redirige al login
    async requiereSesion() {
        const sesion = await this.obtenerSesion();

        if (!sesion || !sesion.usuario) {
            window.location.href = "login.html";
            return null;
        }

        return sesion.usuario;
    },

    // comprueba los roles 
    esAdministrador(usuario) {
        return usuario?.role === "ADMIN";
    },

    esConserje(usuario) {
        return usuario?.role === "USER";
    }
};
