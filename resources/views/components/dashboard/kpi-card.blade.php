@props(['title', 'value', 'subtitle', 'variant' => 'gold', 'comparison' => null])

<div class="kpi-card kpi-{{ $variant }}">
    <div class="kpi-label">{{ $title }}</div>
    <div class="kpi-value" style="{{ $variant === 'gold' ? 'color:#8a5a20;' : ($variant === 'roast' ? 'color:#4a2518;' : ($variant === 'red' ? 'color:#8a2020;' : 'color:#2d5a2d;')) }}">
        {{ $value }}
    </div>
    <div class="kpi-sub">{{ $subtitle }}</div>
    @if($comparison)
        <div class="kpi-sub" style="color: {{ $comparison['positive'] ? '#2d5a2d' : '#c0392b' }}; font-size: 0.65rem;">
            {{ $comparison['positive'] ? '↑' : '↓' }} {{ $comparison['percentage'] }}% vs {{ $comparison['period'] }}
        </div>
    @endif
</div>
