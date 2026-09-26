@php
    // Kalau someday ada dua flash sekaligus, danger ditampilkan lebih dulu
    // supaya pesan yang penting lebih kelihatan.
    $flashes = array_filter([
        'success' => session('success'),
        'error' => session('error'),
    ]);
@endphp

@foreach ($flashes as $kind => $message)
    <div class="alert alert-{{ $kind === 'error' ? 'danger' : 'success' }}" role="status">
        {{ $message }}
    </div>
@endforeach
