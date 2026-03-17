<div>
    @if (session('notification_saved'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('notification_saved') }}
        </div>
    @endif

    <h3 class="font-display font-bold text-lg text-ink mb-1">Email Notifications</h3>
    <p class="text-sm text-ink2 mb-6">Choose which email notifications you'd like to receive.</p>

    <div class="space-y-4">
        <label class="flex items-center justify-between p-4 bg-bg rounded-lg cursor-pointer group">
            <div>
                <span class="text-sm font-medium text-ink">New Enrollment</span>
                <p class="text-xs text-ink3 mt-0.5">Get notified when you enrol in a new course</p>
            </div>
            <input type="checkbox" wire:model.live="emailEnrollment" class="rounded border-rule text-primary focus:ring-primary">
        </label>

        <label class="flex items-center justify-between p-4 bg-bg rounded-lg cursor-pointer group">
            <div>
                <span class="text-sm font-medium text-ink">Course Completion</span>
                <p class="text-xs text-ink3 mt-0.5">Get notified when you complete a course</p>
            </div>
            <input type="checkbox" wire:model.live="emailCourseComplete" class="rounded border-rule text-primary focus:ring-primary">
        </label>

        <label class="flex items-center justify-between p-4 bg-bg rounded-lg cursor-pointer group">
            <div>
                <span class="text-sm font-medium text-ink">Certificates</span>
                <p class="text-xs text-ink3 mt-0.5">Get notified when a certificate is issued</p>
            </div>
            <input type="checkbox" wire:model.live="emailCertificate" class="rounded border-rule text-primary focus:ring-primary">
        </label>

        <label class="flex items-center justify-between p-4 bg-bg rounded-lg cursor-pointer group">
            <div>
                <span class="text-sm font-medium text-ink">Promotions & Updates</span>
                <p class="text-xs text-ink3 mt-0.5">Receive course recommendations and platform updates</p>
            </div>
            <input type="checkbox" wire:model.live="emailPromotions" class="rounded border-rule text-primary focus:ring-primary">
        </label>
    </div>

    <div class="mt-6">
        <button wire:click="save" class="px-6 py-2.5 bg-ink text-white font-display font-bold text-sm rounded-card hover:opacity-90 transition-opacity">
            Save Preferences
        </button>
    </div>

    <div class="mt-8 pt-6 border-t border-rule">
        <h3 class="font-display font-bold text-lg text-ink mb-1">Push Notifications</h3>
        <p class="text-sm text-ink2 mb-4">Receive real-time browser notifications.</p>
        @livewire('push-notification-toggle')
    </div>
</div>
