<li class="media pb-3" style="page-break-inside: avoid;">
    <div class="media-body py-3 px-3 border bg-light">
        <div class="mb-2">
            @if($comment->user)
                @if($comment->user->username)
                    <strong>{{ $comment->user->username }} ({{ $comment->user->name }} {{ $comment->user->surname }})</strong>
                @else
                    <strong>{{ $comment->user->name }} {{ $comment->user->surname }}</strong>
                @endif
            @else
                <strong>Usuario no identificado</strong>
            @endif
            @if($comment->created_at)
                <small class="text-secondary">
                    {{ \Carbon\Carbon::create($comment->created_at->toDateTimeString())->formatLocalized('%d %b %Y, %H:%M') }} Hrs. (UTC)
                </small>
            @endif
        </div>
        <p>{{ $comment->body }}</p>
        <div class="text-right">
            <span class="mr-3" style="color: #28a745;"><i class="fas fa-thumbs-up"></i> {{ $comment->votos_a_favor }}</span>
            <span style="color: #f83f37;"><i class="fas fa-thumbs-down"></i> {{ $comment->votos_en_contra }}</span>
        </div>
    </div>
</li>
