<div>
    <h4 class="mt-3 mb-3">Información del Artículo</h4>
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
                <td>{{ $article->id }}</td>
            </tr>
            <tr>
                <th>Título</th>
                <td>{{ $article->titulo }}</td>
            </tr>
            <tr>
                <th>Detalle</th>
                <td>{{ $article->detalle }}</td>
            </tr>
            <tr>
                <th>Cantidad de votos totales</th>
                <td>{{ $article->votes()->count() }}</td>
            </tr>
            <tr>
                <th>Cantidad de comentarios totales</th>
                <td>{{ $article->comments()->count() }}</td>
            </tr>
        </tbody>
    </table>
    <table class="table table-striped" style="page-break-inside: avoid;">
        <thead>
            <tr>
                <th colspan="2">Votación del artículo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @if($article->votes()->count() > 0)
                    <td style="width: 30%; vertical-align: middle;">
                        <div id="votacion-idea-{{ $article->id }}-pie-chart"></div>
                    </td>
                    <td>
                        De un total de {{ $article->votes()->count() }} votos totales:
                        <ul>
                            <li>Hubo {{ $article->votos_a_favor }} a favor, correspondiente al {{ round($article->votos_a_favor/$article->votes()->count() * 100, 2) }}% de los votos.</li>
                            <li>Hubo {{ $article->votos_en_contra }} en contra, correspondiente al {{ round($article->votos_en_contra/$article->votes()->count() * 100, 2) }}% de los votos.</li>
                            <li>Hubo {{ $article->abstencion }} abstenciones, correspondiente al {{ round($article->abstencion/$article->votes()->count() * 100, 2) }}% de los votos.</li>
                        </ul>
                    </td>
                @else
                    <td colspan="2">
                        <div class="alert alert-warning mb-0">
                            <span><i class="fas fa-exclamation-circle"></i> El artículo no cuenta con votos.</span>
                        </div>
                    </td>
                @endif
            </tr>
        </tbody>
    </table>
    <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Totales del Artículo</h5>
    <ul class="list-unstyled">
        @if(count($articleCommentsRanking->total_all_votes) > 0)
            @foreach($articleCommentsRanking->total_all_votes as $comment)
                @component('report.components.comment', ['comment' => $comment])
                @endcomponent
            @endforeach
        @else
            <div class="alert alert-warning">
                <span><i class="fas fa-exclamation-circle"></i> El artículo no cuenta con comentarios.</span>
            </div>
        @endif
    </ul>
    <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Positivas del Artículo</h5>
    <ul class="list-unstyled">
        @if(count($articleCommentsRanking->accord_votes) > 0)
            @foreach($articleCommentsRanking->accord_votes as $comment)
                @component('report.components.comment', ['comment' => $comment])
                @endcomponent
            @endforeach
        @else
            <div class="alert alert-warning">
                <span><i class="fas fa-exclamation-circle"></i> El artículo no cuenta con comentarios.</span>
            </div>
        @endif
    </ul>
    <h5 class="mt-3 mb-2">Comentarios con Mayor Cantidad de Reacciones Negativas del Artículo</h5>
    <ul class="list-unstyled">
        @if(count($articleCommentsRanking->desaccord_votes) > 0)
            @foreach($articleCommentsRanking->desaccord_votes as $comment)
                @component('report.components.comment', ['comment' => $comment])
                @endcomponent
            @endforeach
        @else
            <div class="alert alert-warning">
                <span><i class="fas fa-exclamation-circle"></i> El artículo no cuenta con comentarios.</span>
            </div>
        @endif
    </ul>
</div>
