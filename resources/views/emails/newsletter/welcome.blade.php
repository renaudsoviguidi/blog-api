<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; background: #f8fafc; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 2rem auto; background: white; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; }
        .header { background: linear-gradient(135deg, #38bdf8, #0284c7); padding: 2rem; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 1.4rem; }
        .body { padding: 2rem; color: #334155; line-height: 1.7; }
        .footer { padding: 1rem 2rem; background: #f8fafc; text-align: center; font-size: .75rem; color: #94a3b8; border-top: 1px solid #f1f5f9; }
        .btn { display: inline-block; padding: .65rem 1.5rem; background: #0284c7; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 1rem 0; }
        a.unsubscribe { color: #94a3b8; font-size: .75rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MonBlog</h1>
        </div>
        <div class="body">
            <p>Bonjour {{ $subscriber->name ?? 'cher lecteur' }},</p>
            <p>Merci de vous être abonné à <strong>MonBlog</strong> ! Vous recevrez désormais nos nouveaux articles directement dans votre boîte mail.</p>
            <p style="text-align:center">
                <a href="{{ env('FRONTEND_URL') }}" class="btn">Découvrir les articles</a>
            </p>
            <p>À très bientôt,<br><strong>L'équipe MonBlog</strong></p>
        </div>
        <div class="footer">
            <a href="{{ env('APP_URL') }}/newsletter/unsubscribe/{{ $subscriber->unsubscribe_token }}" class="unsubscribe">
                Se désabonner
            </a>
        </div>
    </div>
</body>
</html>