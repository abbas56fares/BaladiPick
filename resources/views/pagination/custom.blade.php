@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="d-flex align-items-center justify-content-between" data-pagination-ajax="true">
        <div class="d-flex align-items-center justify-content-between" style="width: 100%;">
            <!-- Previous Page Link -->
            <div>
                @if ($paginator->onFirstPage())
                    <span class="btn btn-sm btn-outline-secondary disabled">
                        <i class="bi bi-chevron-left"></i> Previous
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-sm btn-outline-secondary pagination-ajax-link">
                        <i class="bi bi-chevron-left"></i> Previous
                    </a>
                @endif
            </div>

            <!-- Result Count Display -->
            <div style="white-space: nowrap;">
                <small class="text-muted">
                    Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
                </small>
            </div>

            <!-- Next Page Link -->
            <div>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-sm btn-outline-secondary pagination-ajax-link">
                        Next <i class="bi bi-chevron-right"></i>
                    </a>
                @else
                    <span class="btn btn-sm btn-outline-secondary disabled">
                        Next <i class="bi bi-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const paginationLinks = document.querySelectorAll('.pagination-ajax-link');
        
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = this.href;
                const nav = this.closest('[data-pagination-ajax="true"]');
                const tableContainer = nav.closest('.card-body') || nav.parentElement;
                
                // Show loading state
                const tableElement = tableContainer.querySelector('table');
                if (tableElement) {
                    tableElement.style.opacity = '0.6';
                    tableElement.style.pointerEvents = 'none';
                }
                
                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Extract the new table and pagination
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('table');
                    const newPagination = doc.querySelector('[data-pagination-ajax="true"]');
                    
                    if (newTable && tableElement) {
                        tableElement.innerHTML = newTable.innerHTML;
                        tableElement.style.opacity = '1';
                        tableElement.style.pointerEvents = 'auto';
                        
                        // Re-attach sorting event listeners to the updated table
                        if (typeof attachTableSorting === 'function') {
                            attachTableSorting(tableElement);
                        }
                    }
                    
                    if (newPagination && nav) {
                        nav.innerHTML = newPagination.innerHTML;
                        // Re-attach event listeners to new pagination links
                        const newLinks = nav.querySelectorAll('.pagination-ajax-link');
                        newLinks.forEach(newLink => {
                            newLink.addEventListener('click', arguments.callee);
                        });
                    }
                    
                    // Scroll to table
                    if (tableElement) {
                        tableElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                })
                .catch(error => {
                    console.error('Pagination error:', error);
                    if (tableElement) {
                        tableElement.style.opacity = '1';
                        tableElement.style.pointerEvents = 'auto';
                    }
                });
            });
        });
    });
    </script>
@endif
