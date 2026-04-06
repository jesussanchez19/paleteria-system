<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de verificación</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; background: #f8fafc; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px;">
        <h1 style="margin-top: 0; font-size: 24px;">Código de verificación</h1>
        <p>Hola {{ $name }},</p>
        <p>Se solicitó verificar este correo para registrar al gerente en el sistema.</p>
        <div style="margin: 24px 0; padding: 16px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; text-align: center;">
            <div style="font-size: 32px; font-weight: 700; letter-spacing: 6px;">{{ $code }}</div>
        </div>
        <p>Este código vence en 10 minutos.</p>
        <p>Si no reconoces esta acción, puedes ignorar este mensaje.</p>
    </div>
</body>
</html>