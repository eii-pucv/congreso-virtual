<template>
    <v-popover
            @show="showNotifications"
            @apply-hide="hideNotifications"
            :popover-class="'custom-popover-theme'"
            :popover-inner-class="'tooltip-inner custom-popover-inner custom-scrollbar-wk custom-scrollbar-mz'"
    >
        <button class="btn text-white tooltip-target">
            <i class="fas fa-bell"></i>
            <span v-if="hasNewNotifications" class="text-info font-12 align-top">
                <i class="fas fa-exclamation-circle" style="position: absolute;"></i>
            </span>
        </button>
        <template slot="popover">
            <router-link class="h5 mb-10" :class="mode==='dark'?'text-white':''" :to="{ path: '/notifications' }">
                {{ $t('navbar.notificaciones.titulo') }}
            </router-link>
            <hr class="my-10">
            <RewardNotificationsList v-if="showNotificationsIsActive"></RewardNotificationsList>
        </template>
    </v-popover>
</template>

<script>
    import RewardNotificationsList from './RewardNotificationsList';

    export default {
        name: 'NotificationsPopover',
        components: {
            RewardNotificationsList
        },
        data() {
            return {
                popoverOptions: {
                    popover: {
                        defaultClass: 'vue-popover-theme',
                        defaultInnerClass: 'tooltip-inner popover-inner vue-popover-inner custom-scrollbar-wk custom-scrollbar-mz'
                    }
                },
                showNotificationsIsActive: false,
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }
        },
        methods: {
            showNotifications() {
                this.showNotificationsIsActive = true;
                this.$store.dispatch('hasNewNotifications', false);
            },
            hideNotifications() {
                this.showNotificationsIsActive = false;
            }
        },
        computed: {
            hasNewNotifications() {
                return this.$store.getters.hasNewNotifications;
            }
        }
    }
</script>

<style>
    .custom-popover-theme {
        width: 400px;
        max-width: 400px !important;
        max-height: 80vh !important;
        padding: 1px 1px 1px 1px;
    }

    .custom-popover-inner {
        width: 396px;
        max-width: 396px !important;
        max-height: 79vh !important;
        overflow-y: auto;
    }
</style>