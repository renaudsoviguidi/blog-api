@extends('emails.include.layout')
@section('container')
    <p class="greeting">Bonjour <strong>{{ $nom_prenoms }}</strong>,</p>

    <p class="message">
        Merci de vous être inscrit sur <strong>MonBlog</strong>.<br />
        Cliquez sur le bouton ci-dessous pour activer votre compte
        et commencer à explorer nos articles.
    </p>

    <div class="cta-wrap">
        <a href="{{ $activationUrl }}" class="cta-btn">
            ✅ &nbsp; Activer mon compte
        </a>
        <p class="cta-expiry">⏳ Ce lien expire dans 60 minutes</p>
    </div>

    <hr class="divider" />

    {{-- Sécurité --}}
    <div class="security-box">
        <div class="security-title">🔒 Informations de sécurité</div>
        <ul>
            <li>Ne partagez jamais ce lien avec quelqu'un d'autre.</li>
            <li>MonBlog ne vous demandera jamais votre mot de passe par email.</li>
            <li>Ce lien est à usage unique et expire après activation.</li>
        </ul>
    </div>

    {{-- Lien de secours si le bouton ne fonctionne pas --}}
    <p class="fallback">
        Le bouton ne fonctionne pas ? Copiez ce lien dans votre navigateur :<br />
        <a href="{{ $activationUrl }}">{{ $activationUrl }}</a>
    </p>

    <p class="no-request">
        Si vous n'avez pas créé de compte sur MonBlog, ignorez cet email.
        Aucune action n'est requise de votre part.
    </p>
@endsection
