<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Arial', sans-serif; direction: rtl; }
        .container { margin: 20px; padding: 20px; border: 1px solid #ccc; }
        .header { font-size: 20px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">تفاصيل الجلسة الخاصة بك</div>
        <p><strong>الاسم:</strong> {{ $booking->user->name }}</p>
        <p><strong>البريد الإلكتروني:</strong> {{ $booking->user->email }}</p>
        <p><strong>الطبيب:</strong> {{ $booking->doctor->user->name }}</p>
        <p><strong>تاريخ الجلسة:</strong> {{ $booking->appointment->date }}</p>
        <p><strong>الوقت:</strong> من {{ $booking->slot_start_time }} إلى {{ $booking->slot_end_time }}</p>
        <p><strong>رابط الجلسة عبر Google Meet:</strong> <a href="{{ $meetLink }}">{{ $meetLink }}</a></p>
        <hr>
        <p>نتمنى لك جلسة مفيدة وصحية!</p>
    </div>
</body>
</html>
