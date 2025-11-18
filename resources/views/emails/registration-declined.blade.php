<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Declined</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #ef4444; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 24px;">Registration Update</h1>
    </div>
    
    <div style="background-color: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
        <p style="font-size: 16px; margin-bottom: 20px;">Hello {{ $registration->user->name }},</p>
        
        <p style="font-size: 16px; margin-bottom: 20px;">
            We regret to inform you that your registration for <strong>{{ $registration->event->title }}</strong> has been declined.
        </p>
        
        <div style="background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ef4444;">
            <h2 style="margin-top: 0; color: #1f2937; font-size: 20px;">Event Details</h2>
            <p style="margin: 10px 0;"><strong>Event:</strong> {{ $registration->event->title }}</p>
            <p style="margin: 10px 0;"><strong>Date:</strong> {{ $registration->event->start_date->format('F d, Y') }}</p>
            <p style="margin: 10px 0;"><strong>Location:</strong> {{ $registration->event->location }}</p>
        </div>
        
        <p style="font-size: 16px; margin-bottom: 20px;">
            This may be due to capacity limitations or other factors. We appreciate your interest and encourage you to register for our future events.
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('events.index') }}" 
               style="display: inline-block; background-color: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Browse Other Events
            </a>
        </div>
        
        <p style="font-size: 14px; color: #6b7280; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
            If you have any questions, please don't hesitate to contact us.
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px;">
        <p>© {{ date('Y') }} Event Management System. All rights reserved.</p>
    </div>
</body>
</html>
