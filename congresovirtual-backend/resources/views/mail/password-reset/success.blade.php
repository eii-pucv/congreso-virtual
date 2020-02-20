@component('mail::message')
<h4>Hola {{ $user->name . ' ' . $user->surname }},</h4>
<p>
    Usted ha cambiado su contraseña satisfactoriamente.<br><br>
    Si ya cambió su contraseña, no se requiere que haga ninguna otra acción.<br>
    Si no cambió su contraseña, proteja su cuenta.<br><br>
    Saludos,<br>
    <b>{{ config('app.name') }}</b>
</p>
@endcomponent
