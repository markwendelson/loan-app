@if ($paginator->hasPages())
<nav>
    <ul class="pagination pagination-sm pagination-primary pagin-border-primary">

        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
        @endphp

        {{-- PREVIOUS --}}
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link"
                wire:click.prevent="previousPage">
                Previous
            </a>
        </li>

        {{-- SMALL PAGES --}}
        @if ($last <= 5)

            @for ($page = 1; $page <= $last; $page++)
                <li class="page-item {{ $page == $current ? 'active' : '' }}"
                    wire:key="page-{{ $page }}">

                    <a class="page-link"
                        wire:click.prevent="gotoPage({{ $page }})">
                        {{ $page }}
                    </a>
                </li>
            @endfor

        @else

            {{-- WINDOW --}}
            @php
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp

            {{-- FIRST --}}
            @if ($start > 1)
                <li class="page-item" wire:key="page-1">
                    <a class="page-link"
                        wire:click.prevent="gotoPage(1)">1</a>
                </li>

                @if ($start > 2)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- MIDDLE --}}
            @for ($page = $start; $page <= $end; $page++)
                <li class="page-item {{ $page == $current ? 'active' : '' }}"
                    wire:key="page-{{ $page }}">

                    <a class="page-link"
                        wire:click.prevent="gotoPage({{ $page }})">
                        {{ $page }}
                    </a>
                </li>
            @endfor

            {{-- LAST --}}
            @if ($end < $last)
                @if ($end < $last - 1)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif

                <li class="page-item" wire:key="page-last">
                    <a class="page-link"
                        wire:click.prevent="gotoPage({{ $last }})">
                        {{ $last }}
                    </a>
                </li>
            @endif
        @endif

        {{-- NEXT --}}
        <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
            <a class="page-link"
                wire:click.prevent="nextPage">
                Next
            </a>
        </li>

    </ul>
</nav>
@endif