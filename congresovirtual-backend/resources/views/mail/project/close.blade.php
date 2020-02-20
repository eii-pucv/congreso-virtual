@component('mail::message')
<h4>Hola {{ "{$user->name} {$user->surname}" }},</h4>
<p>
    Un proyecto de ley ha cerrado sus votaciones<br>
    ¡Mira los resultados en <b>Congreso Virtual</b>!
</p>
<br>
    <div class="card" style="display: inline-block">
        <img src="{{ config('app.url') . "/api/storage/projects/{$project->id}/{$project->imagen}" }}" class="card-img-top" alt="{{ $project->titulo }}">
        <div class="card-body">
            <h5 class="card-title">{{ $project->titulo }}</h5>
            <p class="card-text">
            {{ substr($project->resumen, 0, 300) }}
            @if(strlen($project->resumen) > 300)
            ...
            @endif
            </p>
            <a href="{{ $url }}" class="btn btn-success">Ver Proyecto</a>
        </div>
    </div>
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
            'actionText' => 'Ver Proyecto',
            'actionURL' => $url
        ]
    )
@endslot
@endcomponent
