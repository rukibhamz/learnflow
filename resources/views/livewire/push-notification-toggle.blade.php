<div x-data="pushNotifications(@js(route('push.subscribe')), @js(route('push.unsubscribe')), @js(route('push.vapid-key')))"
     class="flex items-center justify-between">
    <div>
        <p class="text-sm font-medium text-ink">Browser Push Notifications</p>
        <p class="text-xs text-ink3">Get notified about new enrollments, completions, and certificates in real time.</p>
    </div>
    <button @click="toggle()"
            :disabled="loading"
            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
            :class="subscribed ? 'bg-primary' : 'bg-gray-300'">
        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow"
              :class="subscribed ? 'translate-x-6' : 'translate-x-1'"></span>
    </button>
</div>

@script
<script>
Alpine.data('pushNotifications', (subscribeUrl, unsubscribeUrl, vapidKeyUrl) => ({
    subscribed: @json($isSubscribed),
    loading: false,

    async toggle() {
        this.loading = true;
        try {
            if (this.subscribed) {
                await this.unsubscribe();
            } else {
                await this.subscribe();
            }
        } catch (e) {
            console.error('Push notification error:', e);
        }
        this.loading = false;
    },

    async subscribe() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            alert('Push notifications are not supported in your browser.');
            return;
        }

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return;

        const reg = await navigator.serviceWorker.register('/sw.js');
        const keyResp = await fetch(vapidKeyUrl);
        const { key } = await keyResp.json();

        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: this.urlBase64ToUint8Array(key),
        });

        const subJson = sub.toJSON();
        await fetch(subscribeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            },
            body: JSON.stringify({
                endpoint: subJson.endpoint,
                keys: subJson.keys,
            }),
        });

        this.subscribed = true;
    },

    async unsubscribe() {
        const reg = await navigator.serviceWorker.getRegistration();
        if (reg) {
            const sub = await reg.pushManager.getSubscription();
            if (sub) {
                await fetch(unsubscribeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ endpoint: sub.endpoint }),
                });
                await sub.unsubscribe();
            }
        }
        this.subscribed = false;
    },

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    },
}));
</script>
@endscript
