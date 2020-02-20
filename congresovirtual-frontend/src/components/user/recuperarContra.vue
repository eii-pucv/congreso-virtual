<template>
    <div class="hk-pg-wrapper hk-auth-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12 pa-0">
                    <div class="auth-form-wrap pt-xl-0 pt-70">
                        <div class="auth-form w-xl-30 w-sm-50 w-100">
                            <form>
                                <h1 class="display-5 mb-30 text-center">{{ $t('reiniciar_contrasena.contenido.titulo') }}</h1>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Nueva contraseña" type="password">
                                    <small>{{ $t('reiniciar_contrasena.contenido.consejo1') }}</small>
                                    <br/>
                                    <small class="consejos">{{ $t('reiniciar_contrasena.contenido.consejo2') }}</small>
                                    <small class="consejos">{{ $t('reiniciar_contrasena.contenido.consejo3') }}</small>
                                    <small class="consejos">{{ $t('reiniciar_contrasena.contenido.consejo4') }}</small>
                                    <small class="consejos">{{ $t('reiniciar_contrasena.contenido.consejo5') }}</small>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input class="form-control" placeholder="Ingresa de nuevo la contraseña" type="password">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><span class="feather-icon"><i data-feather="eye-off"></i></span></span>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-block mb-20" type="submit">
                                    <p class="text-lowercase">{{ $t('reiniciar_contrasena.contenido.reiniciar') }}</p>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>   
</template>

<style scoped>
    .consejos{
        Color: #c44d4d;
    }
</style>