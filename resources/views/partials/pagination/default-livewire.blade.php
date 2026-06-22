@if ($paginator->hasPages())
<nav>
    <ul class="pagination pagination-sm pagination-primary pagin-border-primary">
        {{-- Previous --}}
        @if (!$paginator->onFirstPage())
            <li class="page-item">
                <a class="page-link" wire:click.prevent="previousPage">Previous</a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @foreach ($paginator->links()->elements[0] as $page => $url)
            <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                <a class="page-link" wire:click.prevent="gotoPage({{ $page }})">{{ $page }}</a>
            </li>
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" wire:click.prevent="nextPage">Next</a>
            </li>
        @endif
    </ul>
</nav>
@endif