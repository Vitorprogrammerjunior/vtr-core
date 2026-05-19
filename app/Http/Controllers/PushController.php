<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|url|max:2048',
            'p256dh'   => 'required|string|max:512',
            'auth'     => 'required|string|max:256',
        ]);

        PushSubscription::updateOrCreate(
            ['user_id' => Auth::id(), 'endpoint' => $request->endpoint],
            ['p256dh' => $request->p256dh, 'auth' => $request->auth]
        );

        return response()->json(['ok' => true]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => 'required|string|max:2048']);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['ok' => true]);
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'body'  => 'required|string|max:500',
            'url'   => 'nullable|string|max:500',
        ]);

        $subscriptions = PushSubscription::where('user_id', Auth::id())->get();

        if ($subscriptions->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'Nenhuma subscription.'], 404);
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('services.vapid.subject'),
                'publicKey'  => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ]);

        $payload = json_encode([
            'title' => $request->title,
            'body'  => $request->body,
            'icon'  => '/icon-192.png',
            'url'   => $request->input('url', '/dashboard'),
        ]);

        foreach ($subscriptions as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint'        => $sub->endpoint,
                    'contentEncoding' => 'aesgcm',
                    'keys'            => ['p256dh' => $sub->p256dh, 'auth' => $sub->auth],
                ]),
                $payload
            );
        }

        $sent = 0;
        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                $sent++;
            } elseif ($report->isSubscriptionExpired()) {
                PushSubscription::where('endpoint', $report->getRequest()->getUri()->__toString())->delete();
            }
        }

        return response()->json(['ok' => true, 'sent' => $sent]);
    }
}

    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => 'required|url|max:2048',
            'p256dh'   => 'required|string|max:512',
            'auth'     => 'required|string|max:256',
        ]);

        PushSubscription::updateOrCreate(
            ['user_id' => Auth::id(), 'endpoint' => $request->endpoint],
            ['p256dh' => $request->p256dh, 'auth' => $request->auth]
        );

        return response()->json(['ok' => true]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate(['endpoint' => 'required|string|max:2048']);

        PushSubscription::where('user_id', Auth::id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Envia uma notificação para todos os dispositivos do usuário autenticado.
     * Usa a Web Push API com autenticação VAPID manual via OpenSSL.
     */
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'body'  => 'required|string|max:500',
            'url'   => 'nullable|string|max:500',
        ]);

        $subscriptions = PushSubscription::where('user_id', Auth::id())->get();

        if ($subscriptions->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'Nenhuma subscription encontrada.'], 404);
        }

        $publicKey  = config('services.vapid.public_key');
        $privateKey = config('services.vapid.private_key');
        $subject    = config('app.url');

        $payload = json_encode([
            'title' => $request->title,
            'body'  => $request->body,
            'icon'  => '/icon-192.png',
            'url'   => $request->input('url', '/dashboard'),
        ]);

        $sent = 0;
        foreach ($subscriptions as $sub) {
            try {
                $this->sendPushViaNode($sub->endpoint, $sub->p256dh, $sub->auth, $payload, $publicKey, $privateKey, $subject);
                $sent++;
            } catch (\Throwable $e) {
                // Remove subscriptions inválidas (410 Gone)
                if (str_contains($e->getMessage(), '410') || str_contains($e->getMessage(), '404')) {
                    $sub->delete();
                }
            }
        }

        return response()->json(['ok' => true, 'sent' => $sent]);
    }

    private function sendPushViaNode(string $endpoint, string $p256dh, string $auth, string $payload, string $publicKey, string $privateKey, string $subject): void
    {
        $script = base_path('push-send.mjs');
        $args   = escapeshellarg(json_encode(compact('endpoint', 'p256dh', 'auth', 'payload', 'publicKey', 'privateKey', 'subject')));
        $output = shell_exec("node {$script} {$args} 2>&1");

        if ($output && str_contains($output, 'ERROR')) {
            throw new \RuntimeException($output);
        }
    }
}
