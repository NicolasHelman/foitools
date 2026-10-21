<div class="wrapper wrapper-login">

    <div class="container container-login animated fadeIn">
        <div class="login">

            <div class="logo-login d-flex align-items-center justify-content-center">
                <img src="../img/logo-foitools.png" alt="FOI Tools" height="70">
            </div>

            <div class="login-heading">
                <h4>Panel de Administración</h4>
                <p>Ingresá tus datos para continuar</p>
            </div>

            <div class="login-form">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="usuario" placeholder=" " required autocomplete="username">
                    <label for="usuario"><i class="fas fa-user me-2"></i>Usuario</label>
                </div>

                <div class="form-floating mb-3 position-relative">
                    <input type="password" class="form-control" id="pass" placeholder=" " required autocomplete="current-password">
                    <label for="pass"><i class="fas fa-key me-2"></i>Contraseña</label>
                    <span class="toggle-password">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>

                <div class="form-action mb-3">
                    <a onclick="ingresar()" class="btn w-100 btn-login" id="btnIngresar">
                        Ingresar
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
