<template>
    <div class="container mt-20">
        <section class="hk-sec-wrapper" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
            <h4 v-if="!inEdition" class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_usuario.titulo1') }}</h4>
            <h4 v-else class="hk-sec-title text-center">{{ $t('administrador.componentes.crear_usuario.titulo2') }}</h4>
            <div class="mt-20 vld-parent">
                <div v-if="loadUser" style="height: 300px;">
                    <Loading
                            :active.sync="loadUser"
                            :is-full-page="fullPage"
                            :height="128"
                            :color="color"
                    ></Loading>
                </div>
                <div v-if="!loadUser">
                    <form @submit.prevent="saveUser">
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-3 mb-10">
                                <label for="name" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.nombre') }}</label>
                                <input
                                        id="name"
                                        v-model="user.name"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="surname" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.apellido') }}</label>
                                <input
                                        id="surname"
                                        v-model="user.surname"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="username" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.usuario') }}</label>
                                <input
                                        id="username"
                                        v-model="user.username"
                                        type="text"
                                        class="form-control"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-3 mb-10">
                                <label for="dni" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.rut') }}</label>
                                <div class="input-group">
                                    <input
                                            id="dni"
                                            v-model="user.dni"
                                            type="text"
                                            class="form-control"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    />
                                </div>
                                <div class="invalid-feedback" :style="mode==='dark'?'color: #fff':''">{{ $t('invalid-feedback.rut') }}</div>
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="email" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.correo') }}</label>
                                <input
                                        id="email"
                                        v-model="user.email"
                                        type="email"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="birthday" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.fecha_nacimiento') }}</label>
                                <DatePicker
                                        id="birthday"
                                        v-model="birthday"
                                        :config="dateOptions"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                ></DatePicker>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-3 mb-10">
                                <label for="pais" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.pais') }}</label>
                                <select
                                        id="pais"
                                        @change="refreshStates(user)"
                                        v-model="user.pais"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                                    <option
                                            v-for="(country, index) in countries"
                                            :key="'country_user-' + index"
                                            :value="country.name"
                                    >
                                        {{ country.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="region" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.region') }}</label>
                                <select
                                        id="region"
                                        @change="refreshCities(user)"
                                        v-model="user.region"
                                        v-bind:disabled="!hasStates(user.pais)"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                                    <option v-if="!hasStates(user.pais)"></option>
                                    <option
                                            v-else
                                            v-for="(state, index) in user.states"
                                            :key="'state_user-' + index"
                                            :value="state"
                                    >
                                        {{ state }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="comuna" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.comuna') }}</label>
                                <select
                                        id="comuna"
                                        v-model="user.comuna"
                                        v-bind:disabled="!hasStates(user.pais) || !user.region"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                                    <option v-if="!hasStates(user.pais) || !user.region"></option>
                                    <option
                                            v-else
                                            v-for="(city, index) in user.cities"
                                            :key="'city_user-' + index"
                                            :value="city"
                                    >
                                        {{ city }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-3 mb-10">
                                <label for="sector" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.sector.titulo') }}</label>
                                <select
                                        id="sector"
                                        v-model="user.sector"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <option
                                            v-for="sector in sectors"
                                            :key="'sector-' + sector.id"
                                            :value="sector.id"
                                    >
                                        {{ sector.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-10">
                                <label for="nivel_educacional" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.nivel.titulo') }}</label>
                                <select
                                        id="nivel_educacional"
                                        v-model="user.nivel_educacional"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                >
                                    <option
                                            v-for="educationalLevel in educationalLevels"
                                            :key="'educational-level-' + educationalLevel.id"
                                            :value="educationalLevel.id"
                                    >
                                        {{ educationalLevel.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-10">
                                <label for="genero" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.genero.titulo') }}</label>
                                <select
                                        id="genero"
                                        v-model="user.genero"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                                    <option
                                            v-for="gender in genders"
                                            :key="'gender-' + gender.id"
                                            :value="gender.id"
                                    >
                                        {{ gender.label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-4 mb-10">
                                <label for="actividad" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.actividad.titulo') }}</label>
                                <select
                                        id="actividad"
                                        v-model="user.actividad"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                                    <option
                                            v-for="activity in activities"
                                            :key="'activity-' + activity.id"
                                            :value="activity.id"
                                    >
                                        {{ activity.label }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-10">
                                <label for="terms" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.temas_interes') }}</label>
                                <multiselect
                                        id="terms"
                                        v-model="currentUserTerms"
                                        track-by="id"
                                        label="value"
                                        :placeholder="$t('perfil_usuario.componentes.edicion_perfil.buscar_tema_interes')"
                                        :options="terms"
                                        :multiple="true"
                                        :showLabels="false"
                                        :style="mode==='dark'?' color: #fff':''"
                                ></multiselect>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-3 mb-10">
                                <label for="rol" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_usuario.rol.titulo') }}</label>
                                <select
                                        id="rol"
                                        v-model="user.rol"
                                        class="form-control custom-select d-block w-100"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                >
                                    <option value="USER">{{ $t('administrador.componentes.crear_usuario.rol.usuario_normal') }}</option>
                                    <option value="ADMIN">{{ $t('administrador.componentes.crear_usuario.rol.administrador') }}</option>
                                </select>
                            </div>
                            <div v-if="!inEdition" class="col-md-4 mb-10">
                                <label for="password" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_usuario.contrasena.titulo') }}</label>
                                <div class="input-group">
                                    <input
                                            id="password"
                                            v-model="user.password"
                                            class="form-control"
                                            required
                                            :type="passwordFieldType"
                                            :style="mode==='dark'?'background: #080035; color: #fff':''"
                                    />
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" @click="switchPasswordVisibility" style="width: 55px;">
                                            <i v-if="passwordFieldType === 'password'" class="fas fa-eye-slash fa-lg"></i>
                                            <i v-else class="fas fa-eye fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div v-if="!inEdition" class="col-md-3 mb-10">
                                <label for="password_confirmation" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_usuario.contrasena.confirme') }}</label>
                                <input
                                        id="password_confirmation"
                                        v-model="user.password_confirmation"
                                        type="password"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: #080035; color: #fff':''"
                                />
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="active"
                                            v-model="user.active"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    />
                                    <label for="active" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_usuario.activo') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="active_gamification"
                                            v-model="user.active_gamification"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    />
                                    <label for="active_gamification" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('administrador.componentes.crear_usuario.gamificacion_activada') }}</label>
                                </div>
                            </div>
                        </div>
                        <div v-if="user.nivel_educacional == 7 || user.nivel_educacional == 8" class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="es_experto"
                                            v-model="user.es_experto"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :disabled="user.es_organizacion"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    />
                                    <label for="es_experto" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.registro_experto') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row align-items-center justify-content-center">
                            <div class="col-md-10 mb-10">
                                <div class="custom-control custom-checkbox checkbox-primary float-left">
                                    <input
                                            id="es_organizacion"
                                            v-model="user.es_organizacion"
                                            type="checkbox"
                                            class="custom-control-input"
                                            :disabled="user.es_experto"
                                            :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                    />
                                    <label for="es_organizacion" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.registrar_organizacion') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-20">
                            <button class="btn btn-primary vld-parent" type="submit">
                                <i class="fas fa-save"></i> {{ $t('guardar') }}
                                <Loading
                                        :active.sync="loadBtnSave"
                                        :is-full-page="fullPage"
                                        :height="24"
                                        :color="'#ffffff'"
                                ></Loading>
                            </button>
                            <button @click.prevent="back" class="btn btn-danger ml-10">
                                <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <section
                v-if="user.es_experto"
                class="hk-sec-wrapper"
                :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
        >
            <h4 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.edicion_perfil.usuario_experto.titulo_registro') }}</h4>
            <div class="mt-20">
                <form @submit.prevent="saveUser">
                    <div class="form-row align-items-center justify-content-center">
                        <div class="col-md-5 mb-10">
                            <label for="titulo_profesional" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.usuario_experto.profesion.titulo') }}</label>
                            <select
                                    id="titulo_profesional"
                                    v-model="user.titulo_profesional"
                                    class="form-control custom-select d-block w-100"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <option
                                        v-for="profession in professions"
                                        :key="'profession-' + profession.id"
                                        :value="profession.id"
                                >
                                    {{ profession.label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-10">
                            <label for="anios_experiencia_laboral" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.usuario_experto.experiencia.titulo') }}</label>
                            <select
                                    id="anios_experiencia_laboral"
                                    v-model="user.anios_experiencia_laboral"
                                    class="form-control custom-select d-block w-100"
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <option
                                        v-for="experience in experiences"
                                        :key="'experience-' + experience.id"
                                        :value="experience.id"
                                >
                                    {{ experience.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row align-items-center justify-content-center">
                        <div class="col-md-10 mb-10">
                            <label for="estudios_adicionales" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.usuario_experto.estudios.titulo') }}</label>
                            <input
                                    id="estudios_adicionales"
                                    v-model="user.estudios_adicionales"
                                    type="text"
                                    class="form-control"
                                    required
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                            <div class="invalid-feedback">{{ $t('invalid-feedback.area') }}</div>
                        </div>
                    </div>
                    <div class="form-row align-items-center justify-content-center">
                        <div class="class col-md-10 mb-10">
                            <label for="areas_desempenio" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.usuario_experto.area_desempenio.titulo') }}</label>
                            <multiselect
                                    id="areas_desempenio"
                                    v-model="user.areas_desempenio"
                                    track-by="id"
                                    label="label"
                                    :placeholder="$t('perfil_usuario.componentes.edicion_perfil.usuario_experto.area_desempenio.buscar')"
                                    :options="areasDesempenio"
                                    :multiple="true"
                                    :showLabels="false"
                                    :style="mode==='dark'?' color: #fff':''"
                            ></multiselect>
                        </div>
                    </div>
                    <div class="form-row align-items-center justify-content-center">
                        <div class="class col-md-10 mb-10">
                            <label for="temas_trabajo" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.usuario_experto.temas') }}</label>
                            <input
                                    id="temas_trabajo"
                                    v-model="user.temas_trabajo"
                                    type="text"
                                    class="form-control"
                                    required
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            />
                            <div class="invalid-feedback">{{ $t('invalid-feedback.temas') }}</div>
                        </div>
                    </div>
                    <div class="text-center mt-20">
                        <button class="btn btn-primary vld-parent" type="submit">
                            <i class="fas fa-save"></i> {{ $t('guardar') }}
                            <Loading
                                    :active.sync="loadBtnSave"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="'#ffffff'"
                            ></Loading>
                        </button>
                        <button @click.prevent="back" class="btn btn-danger ml-10">
                            <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
        <section
                v-else-if="user.es_organizacion"
                class="hk-sec-wrapper"
                :style="mode==='dark'?'background: rgb(12, 1, 80);':''"
        >
            <h4 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.titulo') }}</h4>
            <div class="mt-20">
                <form @submit.prevent="saveUser">
                    <div class="form-row align-items-center justify-content-center">
                        <div class="col-md-3 mb-10">
                            <label for="nombre_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.nombre') }}</label>
                            <input
                                    id="nombre_org"
                                    v-model="user.nombre_org"
                                    type="text"
                                    class="form-control"
                                    required
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            />
                        </div>
                        <div class="col-md-4 mb-10">
                            <label for="email_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.correo') }}</label>
                            <input
                                    id="email_org"
                                    v-model="user.email_org"
                                    type="email"
                                    class="form-control"
                                    required
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            />
                        </div>
                        <div class="col-md-3 mb-10">
                            <label for="enlace_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.enlace') }}</label>
                            <input
                                    id="enlace_org"
                                    v-model="user.enlace_org"
                                    type="text"
                                    class="form-control"
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            />
                        </div>
                    </div>
                    <div class="form-row align-items-center justify-content-center">
                        <div class="col-md-10 mb-10">
                            <div class="custom-control custom-checkbox checkbox-primary">
                                <input
                                        id="tiene_per_jur"
                                        v-model="user.tiene_per_jur"
                                        type="checkbox"
                                        class="custom-control-input"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                                <label for="tiene_per_jur" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.per_jur') }}</label>
                            </div>
                        </div>
                    </div>
                    <div v-if="user.tiene_per_jur" class="form-row align-items-center justify-content-center">
                        <div class="col-md-3 mb-10">
                            <label for="dni_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.rut') }}</label>
                            <div class="input-group">
                                <input
                                        id="dni_org"
                                        v-model="user.dni_org"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                            <div class="invalid-feedback" :style="mode==='dark'?'color: #fff':''">{{ $t('invalid-feedback.rut') }}</div>
                        </div>
                        <div class="col-md-4 mb-10">
                            <label for="rep_legal_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.representante') }}</label>
                            <div class="input-group">
                                <input
                                        id="rep_legal_org"
                                        v-model="user.rep_legal_org"
                                        type="text"
                                        class="form-control"
                                        required
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                />
                            </div>
                            <div class="invalid-feedback" :style="mode==='dark'?'color: #fff':''">{{ $t('invalid-feedback.representante') }}</div>
                        </div>
                        <div class="col-md-3 mb-10">
                            <label for="tipo_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.tipo.titulo') }}</label>
                            <select
                                    id="tipo_org"
                                    v-model="user.tipo_org"
                                    class="form-control custom-select d-block w-100"
                                    required
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            >
                                <option value="1">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.tipo.con_fines_lucro') }}</option>
                                <option value="2">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.tipo.sin_fines_lucro') }}</option>
                            </select>
                        </div>
                    </div>
                    <hr />
                    <h4 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.miembros.titulo') }}</h4>
                    <div
                            class="form-row align-items-center justify-content-center"
                            v-for="(member, index) in user.member_orgs"
                            :key="'member-' + index"
                    >
                        <div class="col-md-10">
                            <hr v-show="index !== 0 && user.member_orgs.length > 1">
                            <div
                                    class="float-right"
                                    v-bind:class="{ 'btn-group': index || (!index && user.member_orgs.length > 1) }"
                            >
                                <a @click="removeMember(index)" v-show="index || (!index && user.member_orgs.length > 1)" class="btn btn-sm btn-danger">
                                    <i class="fas fa-minus"></i>
                                </a>
                                <a @click="addMember" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-10">
                            <label for="name_member_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.miembros.nombre') }}</label>
                            <input
                                    id="name_member_org"
                                    v-model="member.name"
                                    type="text"
                                    class="form-control"
                                    required
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            />
                        </div>
                        <div class="col-md-4 mb-10">
                            <label for="surname_member_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.miembros.apellidos') }}</label>
                            <input
                                    id="surname_member_org"
                                    v-model="member.surname"
                                    type="text"
                                    class="form-control"
                                    required
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            />
                        </div>
                        <div class="col-md-3 mb-10">
                            <label for="dni_member_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.miembros.rut') }}</label>
                            <input
                                    id="dni_member_org"
                                    v-model="member.dni"
                                    type="text"
                                    class="form-control"
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            />
                        </div>
                    </div>
                    <hr />
                    <h4 class="hk-sec-title text-center">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.ubicaciones.titulo') }}</h4>
                    <div
                            class="form-row align-items-center justify-content-center mb-20"
                            v-for="(location, index) in user.location_orgs"
                            :key="'location-' + index"
                    >
                        <div class="col-md-10">
                            <hr v-show="index !== 0 && user.location_orgs.length > 1">
                            <div
                                    class="float-right"
                                    v-bind:class="{ 'btn-group': index || (!index && user.location_orgs.length > 1) }"
                            >
                                <a @click="removeLoaction(index)" v-show="index || (!index && user.location_orgs.length > 1)" class="btn btn-sm btn-danger">
                                    <i class="fas fa-minus"></i>
                                </a>
                                <a @click="addLocation" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-7 mb-10">
                            <label for="direccion_location_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.ubicaciones.direccion') }}</label>
                            <input
                                    id="direccion_location_org"
                                    v-model="location.direccion"
                                    type="text"
                                    class="form-control"
                                    required
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            />
                        </div>
                        <div class="col-md-3 mb-10">
                            <label for="sector_location_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.ubicaciones.sector.titulo') }}</label>
                            <select
                                    id="sector_location_org"
                                    v-model="location.sector"
                                    class="form-control custom-select d-block w-100"
                                    required
                                    :style="mode==='dark'?'background: #080035; color: #fff':''"
                            >
                                <option
                                        v-for="orgSector in orgSectors"
                                        :key="'org-sector-' + orgSector.id"
                                        :value="orgSector.id"
                                >
                                    {{ orgSector.label }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-10">
                            <label for="pais_location_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.ubicaciones.pais') }}</label>
                            <select
                                    id="pais_location_org"
                                    @change="refreshStates(location)"
                                    v-model="location.pais"
                                    class="form-control custom-select d-block w-100"
                                    required
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            >
                                <option
                                        v-for="(country, index) in countries"
                                        :key="'country_location_org-' + index"
                                        :value="country.name"
                                >
                                    {{ country.name }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-10">
                            <label for="region_location_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.ubicaciones.region') }}</label>
                            <select
                                    id="region_location_org"
                                    @change="refreshCities(location)"
                                    v-model="location.region"
                                    v-bind:disabled="!hasStates(location.pais)"
                                    class="form-control custom-select d-block w-100"
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            >
                                <option v-if="!hasStates(location.pais)"></option>
                                <option
                                        v-else
                                        v-for="(state, index) in location.states"
                                        :key="'state_location_org-' + index"
                                        :value="state"
                                >
                                    {{ state }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-10">
                            <label for="comuna_location_org" :style="mode==='dark'?'color: #fff':''">{{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.ubicaciones.comuna') }}</label>
                            <select
                                    id="comuna_location_org"
                                    v-model="location.comuna"
                                    v-bind:disabled="!hasStates(location.pais) || !location.region"
                                    class="form-control custom-select d-block w-100"
                                    :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                            >
                                <option v-if="!hasStates(location.pais) || !location.region"></option>
                                <option
                                        v-else
                                        v-for="(city, index) in location.cities"
                                        :key="'city_location_org-' + index"
                                        :value="city"
                                >
                                    {{ city }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-10 mb-10">
                            <div class="custom-control custom-radio radio-primary">
                                <input
                                        :id="'es_principal_location_org-' + index"
                                        name="es_principal"
                                        v-model="location.es_principal"
                                        v-bind:value="true"
                                        type="radio"
                                        class="custom-control-input"
                                        :style="mode==='dark'?'background: rgb(12, 1, 80); color: #fff':''"
                                        @click="changeEsPrincipal(index)"
                                />
                                <label :for="'es_principal_location_org-' + index" class="custom-control-label" :style="mode==='dark'?'color: #fff':''">
                                    {{ $t('perfil_usuario.componentes.edicion_perfil.organizacion.ubicaciones.principal') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-20">
                        <button class="btn btn-primary vld-parent" type="submit">
                            <i class="fas fa-save"></i> {{ $t('guardar') }}
                            <Loading
                                    :active.sync="loadBtnSave"
                                    :is-full-page="fullPage"
                                    :height="24"
                                    :color="'#ffffff'"
                            ></Loading>
                        </button>
                        <button @click.prevent="back" class="btn btn-danger ml-10">
                            <i class="fas fa-window-close"></i> {{ $t('cancelar') }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</template>

<script>
    import Loading from 'vue-loading-overlay';
    import DatePicker from 'vue-bootstrap-datetimepicker';
    import Multiselect from 'vue-multiselect';
    import axios from '../../../backend/axios';
    import CountriesStatesCities from '../../../data/paises_estados_ciudades.json';

    export default {
        name: 'CreateOrEditUser',
        components: {
            Loading,
            DatePicker,
            Multiselect
        },
        props: {
            user_id: Number
        },
        data() {
            return {
                user: {
                    name: null,
                    surname: null,
                    username: null,
                    rol: null,
                    email: null,
                    active: true,
                    password: null,
                    password_confirmation: null,
                    birthday: null,
                    dni: null,
                    pais: null,
                    region: null,
                    comuna: null,
                    sector: null,
                    nivel_educacional: null,
                    genero: null,
                    actividad: null,
                    active_gamification: true,
                    es_experto: false,
                    titulo_profesional: null,
                    estudios_adicionales: null,
                    anios_experiencia_laboral: null,
                    areas_desempenio: null,
                    temas_trabajo: null,
                    es_organizacion: false,
                    nombre_org: null,
                    email_org: null,
                    enlace_org: null,
                    tiene_per_jur: false,
                    dni_org: null,
                    rep_legal_org: null,
                    tipo_org: 1,
                    states: [],   // Temporal array
                    cities: [],   // Temporal array
                    member_orgs: [
                        {
                            name: null,
                            surname: null,
                            dni: null
                        }
                    ],
                    location_orgs: [
                        {
                            es_principal: true,
                            direccion: null,
                            pais: null,
                            region: null,
                            comuna: null,
                            sector: 1,
                            states: [],  // Temporal array
                            cities: []   // Temporal array
                        }
                    ]
                },
                birthday: null,
                currentUserTerms: [],
                oldUserTerms: [],
                terms: [],
                countriesStatesCities: [],
                countries: [],
                sectors: this.$t('perfil_usuario.componentes.edicion_perfil.sector.opciones'),
                genders: this.$t('perfil_usuario.componentes.edicion_perfil.genero.opciones'),
                educationalLevels: this.$t('perfil_usuario.componentes.edicion_perfil.nivel.opciones'),
                activities: this.$t('perfil_usuario.componentes.edicion_perfil.actividad.opciones'),
                passwordFieldType: 'password',
                professions: this.$t('perfil_usuario.componentes.edicion_perfil.usuario_experto.profesion.opciones'),
                experiences: this.$t('perfil_usuario.componentes.edicion_perfil.usuario_experto.experiencia.opciones'),
                areasDesempenio: this.$t('perfil_usuario.componentes.edicion_perfil.usuario_experto.area_desempenio.opciones'),
                orgSectors: this.$t('perfil_usuario.componentes.edicion_perfil.organizacion.ubicaciones.sector.opciones'),
                inEdition: false,
                loadUser: true,
                loadBtnSave: false,
                fullPage: false,
                color: '#000000',
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.configComponent();

            if(this.user_id) {
                this.getUser();
                this.inEdition = true;
            } else {
                this.loadUser = false;
                if(this.$route.query.es_organizacion === '1') {
                    this.user.es_organizacion = true;
                }
            }
            this.getTerms();
        },
        methods: {
            configComponent() {
                this.countriesStatesCities = CountriesStatesCities;
                this.countriesStatesCities.forEach(country => {
                    this.countries.push({
                        name: country.name,
                        has_states: !!country.states
                    });
                });
            },
            getUser() {
                axios
                    .get('/users/' + this.user_id)
                    .then(res => {
                        this.refreshUser(res.data);
                    })
                    .finally(() => {
                        this.loadUser = false;
                    });
            },
            getTerms() {
                axios
                    .get('/terms', {
                        params: {
                            'return_all': 1
                        }
                    })
                    .then(res => {
                        let terms = res.data.results;
                        this.terms = terms.filter(term => {
                            if(term.taxonomies[0]) {
                                return term.taxonomies[0].value === 'Categorías';
                            }
                        });
                    });
            },
            saveUser() {
                this.loadBtnSave = true;
                this.user.birthday = this.birthday ? this.$moment(this.birthday, this.$t('componentes.moment.formato_editable_sin_hora')).format('YYYY-MM-DD') : null;
                if(this.user.id) {
                    axios
                        .put('/users/' + this.user.id, this.user)
                        .then(() => {
                            if(JSON.stringify(this.currentUserTerms) !== JSON.stringify(this.oldUserTerms)) {
                                axios
                                    .delete('/users/' + this.user.id + '/terms')
                                    .then(() => {
                                        let termsId = this.currentUserTerms.map(term => term.id);
                                        axios
                                            .post('/users/' + this.user.id + '/terms', {
                                                terms_id: termsId
                                            })
                                            .then(() => {
                                                this.oldUserTerms = this.currentUserTerms;
                                                this.loadBtnSave = false;
                                                this.$toastr('success', this.$t('administrador.componentes.crear_usuario.mensajes.exito.modificado.generico.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.exito.modificado.generico.titulo'));
                                            });
                                    })
                                    .catch(() => {
                                        this.loadBtnSave = false;
                                        this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.modificado.terminos.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.modificado.terminos.titulo'));
                                    });
                            } else {
                                this.loadBtnSave = false;
                                this.$toastr('success', this.$t('administrador.componentes.crear_usuario.mensajes.exito.modificado.generico.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.exito.modificado.generico.titulo'));
                            }
                        })
                        .catch(error => {
                            this.loadBtnSave = false;
                            let errorType = error.response.data.error;
                            switch (errorType) {
                                case 'USER_EMAIL_EXISTS_ERROR':
                                    this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.modificado.correo_existe.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.modificado.correo_existe.titulo'));
                                    break;
                                case 'USER_USERNAME_EXISTS_ERROR':
                                    this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.modificado.alias_existe.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.modificado.alias_existe.titulo'));
                                    break;
                                default:
                                    this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.modificado.generico.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.modificado.generico.titulo'));
                                    break;
                            }
                        });
                } else {
                    if(this.user.password === this.user.password_confirmation) {
                        if(this.user.password.length >= 8 && this.user.password.length <= 20) {
                            axios
                                .post('/users', this.user)
                                .then(res => {
                                    let userData = res.data.data;
                                    if(this.currentUserTerms.length > 0) {
                                        let termsId = this.currentUserTerms.map(term => term.id);
                                        axios
                                            .post('/users/' + userData.id + '/terms', {
                                                terms_id: termsId
                                            })
                                            .then(() => {
                                                userData.terms = this.currentUserTerms;
                                                this.refreshUser(userData);
                                                this.inEdition = true;
                                                this.loadBtnSave = false;
                                                this.$toastr('success', this.$t('administrador.componentes.crear_usuario.mensajes.exito.guardado.generico.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.exito.guardado.generico.titulo'));
                                            })
                                            .catch(() => {
                                                this.loadBtnSave = false;
                                                this.inEdition = true;
                                                this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.terminos.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.terminos.titulo'));
                                            });
                                    } else {
                                        this.refreshUser(userData);
                                        this.inEdition = true;
                                        this.loadBtnSave = false;
                                        this.$toastr('success', this.$t('administrador.componentes.crear_usuario.mensajes.exito.guardado.generico.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.exito.guardado.generico.titulo'));
                                    }
                                })
                                .catch(error => {
                                    this.loadBtnSave = false;
                                    let errorType = error.response.data.error;
                                    switch (errorType) {
                                        case 'USER_EMAIL_EXISTS_ERROR':
                                            this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.correo_existe.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.correo_existe.titulo'));
                                            break;
                                        case 'USER_USERNAME_EXISTS_ERROR':
                                            this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.alias_existe.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.alias_existe.titulo'));
                                            break;
                                        default:
                                            this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.generico.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.generico.titulo'));
                                            break;
                                    }
                                });
                        } else {
                            this.loadBtnSave = false;
                            this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.contrasena_no_valida.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.contrasena_no_valida.titulo'));
                        }
                    } else {
                        this.loadBtnSave = false;
                        this.$toastr('error', this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.contrasena_no_coincide.cuerpo'), this.$t('administrador.componentes.crear_usuario.mensajes.fallido.guardado.contrasena_no_coincide.titulo'));
                    }
                }
            },
            refreshUser(userData) {
                this.user = userData;
                this.birthday = this.user.birthday ? this.$moment.utc(this.user.birthday, 'YYYY-MM-DD') : null;
                this.currentUserTerms = this.user.terms;
                this.oldUserTerms = this.currentUserTerms;

                this.user.states = [];  // Temporal array
                this.user.cities = [];  // Temporal array
                if(this.user.pais) {
                    this.getStates(this.user);
                    this.getCities(this.user);
                }

                if(this.user.es_experto === null) this.user.es_experto = false;
                if(this.user.es_organizacion === null) this.user.es_organizacion = false;
                if(this.user.tiene_per_jur === null) this.user.tiene_per_jur = false;

                if(this.user.member_orgs.length === 0) {
                    this.user.member_orgs.push({
                        name: null,
                        surname: null,
                        dni: null
                    });
                }
                if(this.user.location_orgs.length === 0) {
                    this.user.location_orgs.push({
                        es_principal: true,
                        direccion: null,
                        pais: null,
                        region: null,
                        comuna: null,
                        sector: 0,
                        states: [],  // Temporal array
                        cities: []   // Temporal array
                    });
                } else {
                    this.user.location_orgs.forEach(location => {
                        if(location.pais) {
                            this.getStates(location);
                            this.getCities(location);
                        }
                    });
                }
            },
            clearExpertForm() {
                this.user.es_experto = false;
                this.user.titulo_profesional = null;
                this.user.estudios_adicionales = null;
                this.user.anios_experiencia_laboral = null;
                this.user.areas_desempenio = null;
                this.user.temas_trabajo = null;
            },
            addMember() {
                this.user.member_orgs.push({
                    name: null,
                    surname: null,
                    dni: null
                });
            },
            removeMember(index) {
                this.user.member_orgs.splice(index, 1);
            },
            addLocation() {
                this.user.location_orgs.push({
                    es_principal: false,
                    direccion: null,
                    pais: null,
                    region: null,
                    comuna: null,
                    sector: 0,
                    states: [],  // Temporal array
                    cities: []   // Temporal array
                });
            },
            removeLoaction(index) {
                this.user.location_orgs.splice(index, 1);
            },
            changeEsPrincipal(locationIndex) {
                this.user.location_orgs.forEach((location, index) => {
                    location.es_principal = index === locationIndex;
                });
            },
            hasStates(countryName) {
                if(countryName) {
                    let country = this.countries.find(country => country.name === countryName);
                    return country.has_states;
                }
                return false;
            },
            getStates(object) {
                if(object.pais) {
                    let countryData = this.countriesStatesCities.find(country => country.name === object.pais);
                    if(countryData && countryData.states) {
                        object.states = Object.keys(countryData.states);
                    }
                }
            },
            getCities(object) {
                if(object.pais && object.region) {
                    let countryData = this.countriesStatesCities.find(country => country.name === object.pais);
                    if(countryData && countryData.states && countryData.states[object.region]) {
                        object.cities = countryData.states[object.region].sort();
                    }
                }
            },
            refreshStates(object) {
                object.region = null;
                this.getStates(object);
            },
            refreshCities(object) {
                object.comuna = null;
                this.getCities(object);
            },
            switchPasswordVisibility() {
                this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password';
            },
            back() {
                this.$router.go(-1);
            }
        },
        computed: {
            dateOptions() {
                return {
                    format: this.$t('componentes.moment.formato_editable_sin_hora'),
                    locale: this.$moment.locale()
                };
            }
        },
        watch: {
            'user.tiene_per_jur': function (value) {
                if(!this.loadUser && !value) {
                    this.user.dni_org = null;
                    this.user.rep_legal_org = null;
                    this.user.tipo_org = 1;
                }
            },
            'user.nivel_educacional': function (value) {
                if(!this.loadUser && value !== 7 && value !== 8) {
                    this.clearExpertForm();
                }
            },
            'user.es_experto': function (value) {
                if(!this.loadUser && !value) {
                    this.clearExpertForm();
                }
            }
        }
    }
</script>