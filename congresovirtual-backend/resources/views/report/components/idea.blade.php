<div>
    <h4 class="mt-3 mb-3">Información de la Idea Fundamental</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 30%;">Ítem</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>ID</th>
                <td>{{ $idea->id }}</td>
            </tr>
            <tr>
                <th>Título</th>
                <td>{{ $idea->titulo }}</td>
            </tr>
            <tr>
                <th>Detalle</th>
                <td>{{ $idea->detalle }}</td>
            </tr>
            <tr>
                <th>Cantidad de votos totales</th>
                <td>{{ $idea->votes()->count() }}</td>
            </tr>
            <tr>
                <th>Cantidad de comentarios totales</th>
                <td>{{ $idea->comments()->count() }}</td>
            </tr>
        </tbody>
    </table>
    <table class="table table-striped" style="page-break-inside: avoid;">
        <thead>
            <tr>
                <th colspan="2">Votación de la idea fundamental</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @if($idea->votes()->count() > 0)
                    <td style="width: 30%; vertical-align: middle;">
                        <div id="votacion-idea-{{ $idea->id }}-pie-chart"></div>
                    </td>
                    <td>
                        De un total de {{ $idea->votes()->count() }} votos totales:
                        <ul>
                            <li>Hubo {{ $idea->votos_a_favor }} a favor, correspondiente al {{ round($idea->votos_a_favor/$idea->votes()->count() * 100, 2) }}% de los votos.</li>
                            <li>Hubo {{ $idea->votos_en_contra }} en contra, correspondiente al {{ round($idea->votos_en_contra/$idea->votes()->count() * 100, 2) }}% de los votos.</li>
                            <li>Hubo {{ $idea->abstencion }} abstenciones, correspondiente al {{ round($idea->abstencion/$idea->votes()->count() * 100, 2) }}% de los votos.</li>
                        </ul>
                    </td>
                @else
                    <td colspan="2">
                        <div class="alert alert-warning mb-0">
                            <span><i class="fas fa-exclamation-circle"></i> La idea fundamental no cuenta con votos.</span>
                        </div>
                    </td>
                @endif
            </tr>
        </tbody>
    </table>
    <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Totales de la Idea Fundamental</h5>
    <ul class="list-unstyled">
        @if(count($ideaCommentsRanking->total_all_votes) > 0)
            @foreach($ideaCommentsRanking->total_all_votes as $comment)
                @component('report.components.comment', ['comment' => $comment])
                @endcomponent
            @endforeach
        @else
            <div class="alert alert-warning">
                <span><i class="fas fa-exclamation-circle"></i> La idea fundamental no cuenta con comentarios.</span>
            </div>
        @endif
    </ul>
    <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Positivas de la Idea Fundamental</h5>
    <ul class="list-unstyled">
        @if(count($ideaCommentsRanking->accord_votes) > 0)
            @foreach($ideaCommentsRanking->accord_votes as $comment)
                @component('report.components.comment', ['comment' => $comment])
                @endcomponent
            @endforeach
        @else
            <div class="alert alert-warning">
                <span><i class="fas fa-exclamation-circle"></i> La idea fundamental no cuenta con comentarios.</span>
            </div>
        @endif
    </ul>
    <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Negativas de la Idea Fundamental</h5>
    <ul class="list-unstyled">
        @if(count($ideaCommentsRanking->desaccord_votes) > 0)
            @foreach($ideaCommentsRanking->desaccord_votes as $comment)
                @component('report.components.comment', ['comment' => $comment])
                @endcomponent
            @endforeach
        @else
            <div class="alert alert-warning">
                <span><i class="fas fa-exclamation-circle"></i> La idea fundamental no cuenta con comentarios.</span>
            </div>
        @endif
    </ul>
</div>
