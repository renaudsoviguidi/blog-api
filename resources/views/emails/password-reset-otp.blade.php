<!DOCTYPE html>
<html>

<body style="font-family: sans-serif; padding: 2rem; color: #334155;">
    <h2>Bonjour {{ $name }},</h2>
    <p>Votre code de réinitialisation est :</p>
    <div
        style="font-size: 2rem; font-weight: bold; letter-spacing: .5rem;
              background: #eff6ff; color: #0284c7; padding: 1rem 2rem;
              border-radius: .75rem; display: inline-block; margin: 1rem 0;">
        {{ $otp }}
    </div>
    <p style="color: #64748b; font-size: .85rem;">Ce code expire dans <strong>15 minutes</strong>.</p>
</body>

</html>
