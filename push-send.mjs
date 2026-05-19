// push-send.mjs — usado pelo PushController via shell_exec
import webpush from 'web-push';

try {
    const data = JSON.parse(process.argv[2]);
    const { endpoint, p256dh, auth, payload, publicKey, privateKey, subject } = data;

    webpush.setVapidDetails(subject, publicKey, privateKey);

    await webpush.sendNotification(
        { endpoint, keys: { p256dh, auth } },
        payload
    );

    process.exit(0);
} catch (err) {
    console.error('ERROR:', err.statusCode || '', err.message);
    process.exit(1);
}
