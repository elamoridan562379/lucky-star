@props(['activities', 'title' => 'Recent Activity', 'viewAllRoute' => null])

<div class="card" style="display: flex; flex-direction: column;">
    <div class="card-header">
        <span class="card-title">{{ $title }}</span>
        @if($viewAllRoute)
            <a href="{{ $viewAllRoute }}" style="font-size:0.72rem; color:var(--caramel); font-weight:700; letter-spacing:0.04em;">View all →</a>
        @endif
    </div>
    <div style="flex: 1; max-height: 400px; overflow-y: auto;">
        @if($activities->isEmpty())
            <div style="padding:2.5rem 1.5rem; text-align:center; color:#c8b0a0;">
                <div style="font-size:1.5rem; margin-bottom:0.5rem;">📋</div>
                <div style="font-size:0.82rem; font-family:'Playfair Display',serif;">No recent activity</div>
            </div>
        @else
            @foreach($activities as $activity)
                <div style="display:flex; align-items:flex-start; gap:0.75rem; padding:0.8rem 1.25rem; border-bottom:1px solid rgba(74,37,24,0.06);">
                    <div style="font-size:1rem; flex-shrink:0;">{{ $activity['icon'] }}</div>
                    <div style="flex:1;">
                        <div style="font-size:0.75rem; font-weight:700; color:#3d2415;">{{ $activity['title'] }}</div>
                        <div style="font-size:0.7rem; color:#9a7a68; margin-top:0.15rem;">{{ $activity['description'] }}</div>
                        <div style="font-size:0.65rem; color:#c8b0a0; margin-top:0.2rem;">{{ $activity['user'] }} • {{ $activity['timestamp'] }}</div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
