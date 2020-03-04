<template>
    <section class="container">
        <div>
            <h5 class="ma-10" :style="mode==='dark'?'color: #fff':''">
                {{ $t('perfil_usuario.componentes.temas_interes.titulo') }}
            </h5>
        </div>
        <div class="form-group">
            <div class="ml-35">
                <div class="row">
                    <div class="col-sm">
                        <div class="col">
                            <div class="row-md-4 mt-15">
                                <form @submit.prevent="submit">
                                    <div v-if="categories.length > 0">
                                        <div v-for="(category, index) in categories" :key="'category-' + index">
                                            <div class="custom-control custom-checkbox">
                                                <input
                                                        type="checkbox"
                                                        class="custom-control-input"
                                                        :name="'check-' + category.id"
                                                        :id="'check-' + category.id"
                                                        v-model="user_preferences"
                                                        :value="category.id"
                                                />
                                                <label
                                                        class="custom-control-label"
                                                        :for="'check-' + category.id"
                                                        :style="mode==='dark'?'color: #fff':''"
                                                >{{ category.value }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-10">{{ $t('guardar') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import axios from "../../backend/axios";
    import { bus } from "../../main";

    export default {
        name: 'TopicsInterest',
        props: {},
        data() {
            return {
                categories: [],
                arrayPreferencias: [],
                user_preferences: [],
                user_id: null,
                old_preferences: [],
                mode: String
            };
        },
        methods: {
            toggle() {
                if (this.mode === "dark") {
                    this.mode = "light";
                } else {
                    this.mode = "dark";
                }
            },

            submit() {
                axios
                    .delete('/users/' + this.user_id + '/terms?terms_id=' + JSON.stringify(this.old_preferences))
                    .then(res => {
                        this.$toastr("success", "Tus preferencias han sido guardadas con éxito", "Datos anteriores eliminados");
                        axios
                            .post('/users/' + this.user_id + '/terms', {
                                terms_id: this.user_preferences
                            })
                            .then(() => {
                                this.$toastr("success", "Tus preferencias han sido guardadas con éxito", "Datos nuevos agregados");
                            })
                            .catch(() => {
                                this.$toastr("error", "Datos no actualizados", "Tus preferencias no han podido guardarse");
                            });
                    })
                    .catch(() => {
                        this.$toastr("error", "Datos no actualizados", "Tus preferencias no han podido eliminarse");
                    });
            }
        },
        mounted() {
            axios
                .get('/auth/user')
                .then(res => {
                    this.user_id = res.data.id;
                    axios
                        .get('/users/' + this.user_id)
                        .then(res => {
                            let en_bruto = res.data.terms;
                            this.user_preferences = en_bruto.map(a => a.id);
                            this.old_preferences = this.user_preferences;
                        });
                })
                .finally(() => {
                    bus.$on(
                        "toggle",
                        function(mode) {
                            this.toggle();
                        }.bind(this)
                    );
                });

            axios
                .get('/terms', this.arrayPreferencias)
                .then(res => {
                    this.categories = res.data.results.filter(categories => {
                        if(categories.taxonomies[0]) {
                            return categories.taxonomies[0].id == 1;
                        }
                    });
                });
        }
    };
</script>

<style>
    .dark {
        color: #fff;
        background: rgb(8, 0, 53);
    }

    .light {
        color: #000;
        background: #fff;
    }
</style>
