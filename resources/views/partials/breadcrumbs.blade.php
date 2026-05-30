<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex flex-wrap items-center gap-1 text-sm text-gray-500" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a href="/" itemprop="item" class="hover:text-emerald-700 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span itemprop="name">Dashboard</span>
            </a>
            <meta itemprop="position" content="1">
        </li>
        @foreach($breadcrumbs as $i => $crumb)
            <li class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </li>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                @if(isset($crumb['url']) && !$loop->last)
                    <a href="{{ $crumb['url'] }}" itemprop="item" class="hover:text-emerald-700 transition">
                        <span itemprop="name">{{ $crumb['label'] }}</span>
                    </a>
                @else
                    <span itemprop="name" class="text-gray-800 font-medium" aria-current="page">{{ $crumb['label'] }}</span>
                @endif
                <meta itemprop="position" content="{{ $i + 2 }}">
            </li>
        @endforeach
    </ol>
</nav>
