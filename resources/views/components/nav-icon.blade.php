@props(['name'])
@php
$paths = [
    'home' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4a1 1 0 001-1v-6h2v6a1 1 0 001 1h4a1 1 0 001-1V10',
    'book' => 'M4 19.5A2.5 2.5 0 016.5 17H20M4 19.5A2.5 2.5 0 006.5 22H20V4H6.5A2.5 2.5 0 004 6.5v13z',
    'users' => 'M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m5-3.13a4 4 0 100-8 4 4 0 000 8zm7 0a4 4 0 100-8 4 4 0 000 8z',
    'chart' => 'M9 17V9m6 8V5m-11 12v-6m16 6H4a1 1 0 01-1-1V4a1 1 0 011-1h16a1 1 0 011 1v13a1 1 0 01-1 1z',
    'folder' => 'M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z',
    'user' => 'M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z',
    'map' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6-13l6 3m0 0l5.447-2.724A1 1 0 0121 5.618v10.764a1 1 0 01-.553.894L15 20m0-13v13',
    'upload' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V3m0 0L7 8m5-5l5 5',
    'database' => 'M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3-3.582 3-8 3-8-1.343-8-3zm0 0v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7M4 12c0 1.657 3.582 3 8 3s8-1.343 8-3',
];
$d = $paths[$name] ?? $paths['folder'];
@endphp
<svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="{{ $d }}"></path>
</svg>
