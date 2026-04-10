@extends('layouts.app')
@section('title', 'Прогресс')

@push('styles')
<style>
.progress-wrap { max-width: 900px; margin: 0 auto; }

.progress-hero {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 20px;
    padding: 32px 36px;
    margin-bottom: 24px;
}
.progress-hero h1 { font-size: 1.4rem; font-weight: 500; color: var(--color-text); margin-bottom: 24px; }

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}
.stat-box { text-align: center; }
.stat-box .value { font-size: 1.8rem; font-weight: 500; color: #f0c040; }
.stat-box .label { font-size: 0.8rem; color: var(--color-muted); margin-top: 4px; }

.bar-label {
    display: flex; justify-content: space-between;
    font-size: 0.82rem; color: var(--color-muted); margin-bottom: 6px;
}
.level-bar {
    background: var(--color-border);
    border-radius: 20px; height: 8px; overflow: hidden;
}
.level-bar-fill {
    height: 100%; border-radius: 20px;
    background: #f0c040;
    transition: width 0.6s ease;
}

.mini-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px; margin-bottom: 24px;
}
.mini-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 14px;
    padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
}
.mini-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.mini-icon.blue   { background: rgba(79,70,229,0.1); }
.mini-icon.green  { background: rgba(56,161,105,0.1); }
.mini-icon.orange { background: rgba(240,192,64,0.1); }
.mini-num { font-size: 1.4rem; font-weight: 500; color: var(--color-text); }
.mini-lbl { font-size: 0.78rem; color: var(--color-muted); }

.chart-card {
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 16px;
    padding: 22px 24px;
    margin-bottom: 18px;
}
.chart-title { font-size: 0.95rem; font-weight: 500; color: var(--color-text); margin-bottom: 16px; }

.empty-state {
    text-align: center; padding: 56px 20px;
    color: var(--color-muted);
    background: var(--color-surface);
    border: 0.5px solid var(--color-border);
    border-radius: 16px;
}
.empty-state .icon { font-size: 2.5rem; margin-bottom: 12px; }
.empty-state p { font-size: 0.9rem; }
.empty-state p + p { margin-top: 6px; font-size: 0.82rem; }
</style>
@endpush

@section('content')
<div class="progress-wrap">

    <div class="progress-hero">
        <h1>Мой прогресс</h1>
        @php
            $names = [1=>'Новичок', 2=>'Начинающий', 3=>'Средний', 4=>'Продвинутый', 5=>'Эксперт'];
            $pct   = (($user->level - 1) / 4) * 100;
        @endphp
        <div class="stats-grid">
            <div class="stat-box">
                <div class="value">{{ $user->level }}/5</div>
                <div class="label">Уровень</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ $user->rating }}</div>
                <div class="label">Рейтинг</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ $totalLessons }}</div>
                <div class="label">Уроков пройдено</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ $totalPuzzles }}</div>
                <div class="label">Задач решено</div>
            </div>
        </div>
        <div class="bar-label">
            <span>{{ $names[$user->level] }}</span>
            <span>{{ $user->level < 5 ? $names[$user->level + 1] : 'Максимум' }}</span>
        </div>
        <div class="level-bar">
            <div class="level-bar-fill" style="width: {{ $pct }}%"></div>
        </div>
    </div>

    <div class="mini-cards">
        <div class="mini-card">
            <div class="mini-icon blue">📚</div>
            <div>
                <div class="mini-num">{{ $totalLessons }}</div>
                <div class="mini-lbl">Уроков завершено</div>
            </div>
        </div>
        <div class="mini-card">
            <div class="mini-icon green">♟</div>
            <div>
                <div class="mini-num">{{ $totalPuzzles }}</div>
                <div class="mini-lbl">Задач решено</div>
            </div>
        </div>
        <div class="mini-card">
            <div class="mini-icon orange">🎯</div>
            <div>
                <div class="mini-num">{{ number_format($avgAccuracy, 1) }}%</div>
                <div class="mini-lbl">Средняя точность</div>
            </div>
        </div>
    </div>

    @if($stats->count())
        <div class="chart-card">
            <div class="chart-title">График рейтинга</div>
            <canvas id="ratingChart" height="80"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-title">Уроки и задачи по дням</div>
            <canvas id="activityChart" height="80"></canvas>
        </div>
    @else
        <div class="empty-state">
            <div class="icon">🏁</div>
            <p>Статистика появится после первых занятий!</p>
            <p>Пройди урок или реши задачу — и прогресс начнёт отображаться.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
@if($stats->count())
<script>
(function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textColor  = isDark ? '#888780' : '#5f5e5a';

    const labels  = {!! $stats->pluck('date')->map(fn($d) => '"'.$d.'"')->join(',') !!};
    const ratings = [{{ $stats->pluck('rating_change')->join(',') }}];
    const lessons = [{{ $stats->pluck('lessons_done')->join(',') }}];
    const puzzles = [{{ $stats->pluck('puzzles_solved')->join(',') }}];

    const baseOpts = {
        plugins: { legend: { labels: { color: textColor } } },
        scales: {
            x: { ticks: { color: textColor }, grid: { color: gridColor } },
            y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: false }
        }
    };

    new Chart(document.getElementById('ratingChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Рейтинг',
                data: ratings,
                borderColor: '#f0c040',
                backgroundColor: 'rgba(240,192,64,0.08)',
                fill: true, tension: 0.4, pointRadius: 4,
            }]
        },
        options: { ...baseOpts, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('activityChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'Уроки',  data: lessons, backgroundColor: '#4f46e5' },
                { label: 'Задачи', data: puzzles, backgroundColor: '#f0c040' },
            ]
        },
        options: { ...baseOpts, plugins: { legend: { position: 'bottom', labels: { color: textColor } } } }
    });
})();
</script>
@endif
@endpush

