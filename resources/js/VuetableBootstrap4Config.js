// Bootstrap4 + FontAwesome Icon
export default {
    table: {
        tableWrapper: '',
        tableHeaderClass: 'mb-0',
        tableBodyClass: 'mb-0',
        tableClass: 'table table-bordered table-hover',
        loadingClass: 'loading',
        ascendingIcon: 'fas fa-chevron-up',
        descendingIcon: 'fas fa-chevron-down',
        ascendingClass: 'sorted-asc',
        descendingClass: 'sorted-desc',
        sortableIcon: 'fas fa-sort',
        detailRowClass: 'vuetable-detail-row',
        handleIcon: 'fas fa-bars text-secondary',
        renderIcon(classes, options) {
            return `<i class="${classes.join(' ')}"></span>`
        }
    },
    pagination: {
        wrapperClass: 'pagination float-right',
        activeClass: 'active bg-primary',
        disabledClass: 'disabled',
        pageClass: 'page-item pl-3 pr-3 pt-2 border',
        linkClass: 'page-link',
        paginationClass: 'pagination',
        paginationInfoClass: 'float-left',
        dropdownClass: 'form-control',
        icons: {
            first: 'fas fa-angle-double-left',
            prev: 'fas fa-angle-left',
            next: 'fas fa-angle-right',
            last: 'fas fa-angle-double-right',
        }
    }
}