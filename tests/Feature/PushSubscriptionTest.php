<?php

namespace Tests\Feature;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_push()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('push.subscribe'), [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'keys' => [
                'p256dh' => 'test-p256dh-key',
                'auth' => 'test-auth-token',
            ],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $user->id,
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
        ]);
    }

    public function test_subscription_is_upserted_on_same_endpoint()
    {
        $user = User::factory()->create();
        $endpoint = 'https://fcm.googleapis.com/fcm/send/test-endpoint';

        PushSubscription::create([
            'user_id' => $user->id,
            'endpoint' => $endpoint,
            'p256dh_key' => 'old-key',
            'auth_token' => 'old-auth',
        ]);

        $this->actingAs($user)->postJson(route('push.subscribe'), [
            'endpoint' => $endpoint,
            'keys' => ['p256dh' => 'new-key', 'auth' => 'new-auth'],
        ]);

        $this->assertDatabaseCount('push_subscriptions', 1);
        $this->assertDatabaseHas('push_subscriptions', ['p256dh_key' => 'new-key']);
    }

    public function test_user_can_unsubscribe()
    {
        $user = User::factory()->create();
        $endpoint = 'https://fcm.googleapis.com/fcm/send/remove-me';

        PushSubscription::create([
            'user_id' => $user->id,
            'endpoint' => $endpoint,
            'p256dh_key' => 'key',
            'auth_token' => 'auth',
        ]);

        $response = $this->actingAs($user)->postJson(route('push.unsubscribe'), [
            'endpoint' => $endpoint,
        ]);

        $response->assertOk();
        $this->assertDatabaseMissing('push_subscriptions', ['endpoint' => $endpoint]);
    }

    public function test_vapid_key_endpoint_returns_key()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('push.vapid-key'));

        $response->assertOk();
        $response->assertJsonStructure(['key']);
    }

    public function test_unauthenticated_cannot_subscribe()
    {
        $response = $this->postJson(route('push.subscribe'), [
            'endpoint' => 'https://test.com',
            'keys' => ['p256dh' => 'x', 'auth' => 'y'],
        ]);

        $response->assertUnauthorized();
    }
}
