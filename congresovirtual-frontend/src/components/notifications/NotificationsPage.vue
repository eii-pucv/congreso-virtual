<template>
    <div class="container mt-30" style="min-height: 820px;">
        <div class="row justify-content-center text-center">
            <div class="hk-sec-wrapper col-md-6" :style="mode==='dark'?'background: rgb(12, 1, 80);':''">
                <h5 class="hk-sec-title mb-10">{{ $t('navbar.notificaciones.titulo') }}</h5>
                <hr class="my-10">
                <RewardNotificationsList></RewardNotificationsList>
            </div>
        </div>
    </div>
</template>

<script>
    import RewardNotificationsList from './RewardNotificationsList';

    export default {
        name: 'NotificationsPage',
        components: {
            RewardNotificationsList
        },
        data() {
            return {
                mode: String
            };
        },
        mounted() {
            if((this.$store.getters.modo_oscuro === 'dark') || (window.location.href.includes('dark'))) {
                this.mode = 'dark';
            } else {
                this.mode = 'light';
            }

            this.$store.dispatch('hasNewNotifications', false);
        }
    }
</script>