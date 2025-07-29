<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{__('messages.expiring_subscription_email')}}</title>
</head>
<body>
    <p>{{__('messages.hello')}} {{ $user->first_name }} {{$user->last_name}},</p>

    <p>{{__('messages.subscription_expiring')}}</p>
    
    <p>{{__('messages.thank_you')}},</p>
</body>
</html>