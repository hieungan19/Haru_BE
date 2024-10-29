<!-- <x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> -->

@component('mail::message')
# Email Verification

Thank you for signing up. 
Your six-digit code is {{$pin}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
