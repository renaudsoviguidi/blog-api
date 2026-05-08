<div class="footer">
    <div class="footer-logo">MonBlog</div>
    <div class="footer-links">
        <a href="{{ config('app.url') }}">Accueil</a>
        <a href="{{ config('app.url') }}/contact">Contact</a>
        <a href="{{ config('app.url') }}/confidentialite">Confidentialité</a>
    </div>
    <div class="footer-copy">
        © {{ date('Y') }} MonBlog — Tous droits réservés<br />
        Cet email a été envoyé à <strong>{{ $email }}</strong>
    </div>
</div>
