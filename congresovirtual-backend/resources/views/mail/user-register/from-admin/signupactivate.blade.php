@component('mail::message')
<h4>Bienvenido(a) {{ $user->name . ' ' . $user->surname }},</h4>
<p>
    ¡Felicidades! Un administrador lo ha inscrito en Congreso Virtual.<br><br>
    Antes de continuar y si está de acuerdo con ser parte de Congreso Virtual,
    solo debe confirmar su cuenta haciendo click en el siguiente botón.<br>
    Tenga en cuenta que su contraseña asignada es <b>{{ $password }}</b> pero
    le recomendamos que la cambie.
</p>
<a href="{{ $url }}" class="btn btn-primary">Confirmar mi Cuenta</a>
<br>
<p>
    Saludos,<br>
    <b>{{ config('app.name') }}</b>
</p>

@slot('subcopy')
    @lang(
        "Si tiene problemas para presionar el botón \":actionText\", copie y pegue la siguiente URL\n".
        'en su navegador de Internet: [:actionURL](:actionURL)',
        [
            'actionText' => 'Confirmar mi Cuenta',
            'actionURL' => $url
        ]
    )
@endslot
@endcomponent
