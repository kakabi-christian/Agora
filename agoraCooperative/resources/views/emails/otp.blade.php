{{-- email/otp.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td style="padding: 20px 0 30px 0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0px 4px 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td align="center" style="padding: 40px 0 30px 0; background-color: #2d89ef;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px;">AgoCooperative</h1>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 40px 30px 40px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #153643; font-size: 18px; font-weight: bold;">
                                        Bonjour,
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 0 30px 0; color: #153643; font-size: 16px; line-height: 24px;">
                                        Vous avez demandé la réinitialisation de votre mot de passe. Veuillez utiliser le code de vérification (OTP) ci-dessous pour poursuivre l'opération. <strong>Ce code est valide pendant 15 minutes.</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding: 20px 0 20px 0;">
                                        <div style="background-color: #f8f9fa; border: 2px dashed #2d89ef; padding: 20px; display: inline-block; border-radius: 10px;">
                                            <span style="font-size: 36px; font-weight: bold; color: #2d89ef; letter-spacing: 10px;">{{ $otp }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 30px 0 0 0; color: #153643; font-size: 14px; line-height: 20px; border-top: 1px solid #eeeeee;">
                                        Si vous n'avez pas demandé ce changement, vous pouvez ignorer cet email en toute sécurité. Votre mot de passe restera inchangé.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="padding: 30px 30px 30px 30px; background-color: #f8f9fa;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="color: #999999; font-size: 12px; text-align: center;">
                                        &copy; {{ date('Y') }} AgoCooperative. Tous droits réservés.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>