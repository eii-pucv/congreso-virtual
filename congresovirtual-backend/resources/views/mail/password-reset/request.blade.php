@component('mail::message')
<h4>Hola {{ $user->name . ' ' . $user->surname }},</h4>
<p>
    Usted ha recibido este mensaje de correo electrónico porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.
</p>
<a href="{{ $url }}" class="btn btn-primary">Restablecer mi Contraseña</a>
<br>
<p>
    Si no solicitó un restablecimiento de contraseña, no se requiere que haga ninguna otra acción.<br><br>
    Saludos,<br>
    <b>{{ config('app.name') }}</b>
</p>

@slot('subcopy')
    @lang(
        "Si tiene problemas para presionar el botón \":actionText\", copie y pegue la siguiente URL\n".
        'en su navegador de Internet: [:actionURL](:actionURL)',
        [
            'actionText' => 'Restablecer mi Contraseña',
            'actionURL' => $url
        ]
    )
@endslot
@endcomponent
