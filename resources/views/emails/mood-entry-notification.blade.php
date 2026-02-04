<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daynotes Reminder</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: Arial, sans-serif;">
<div style="max-width: 500px; margin: 40px auto; padding: 0 20px;">
    <div style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
        <div style="padding: 24px 32px; border-bottom: 1px solid #e4e4e7; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 600; color: #18181b;">Daynotes</h1>
        </div>
        <div style="padding: 32px;">
            <p style="margin: 0 0 16px; font-size: 16px; line-height: 1.5; color: #3f3f46;">Hello {{ $mailData['name'] }},</p>
            <p style="margin: 0; font-size: 16px; line-height: 1.5; color: #3f3f46;">Please write your mood entry for {{ $mailData['time'] }}.</p>
        </div>
        <div style="padding: 20px 32px; background-color: #fafafa; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #71717a;">Thank you for using Daynotes!</p>
        </div>
    </div>
</div>
</body>

</html>
