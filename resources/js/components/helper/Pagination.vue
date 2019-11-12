<template>
    <nav class="pagination">
        <span class="page-stats">Showing {{ pagination.from }} - {{ pagination.to }} of {{ pagination.total }}</span>
        <button v-if="pagination.prevPageUrl" class="btn btn-outline-dark btn-sm  pagination-previous" 
            @click="$emit('pageClicked', pagination.prevPageUrl)">
            Prev
        </button>
        <button v-else class="btn btn-outline-dark btn-sm  pagination-previous disabled" 
            :disabled="true">
            Prev
        </button>

        <button v-for="page in this.renderedPages" :key="page" 
            class="btn btn-outline-dark btn-sm ml-1 mr-1"
            :class="{'active': page == pagination.currentPage}"
            @click="$emit('pageClicked', pagination.lastPageUrl.replace(/page=\d/g, 'page=' + page))">
            {{ page }}
        </button>

        <button v-if="pagination.nextPageUrl" class="btn btn-outline-dark btn-sm  pagination-next " 
            @click="$emit('pageClicked', pagination.nextPageUrl)">
            Next
        </button>
        <button v-else class="btn btn-outline-dark btn-sm  pagination-next disabled" 
            :disabled="true">
            Next
        </button>

    </nav>
</template>

<script>
    export default {
        props: { 
            pagination: { 
                type: Object,
                required: true,
            },
            eachSide: {
                type: Number,
                default: 2,
                required: false
            }
        },
        computed: {
            pages() {
                let pages = []
                for(let i = 1; i <= this.pagination.lastPage; i++) {
                    pages.push(i)
                }
                return pages
            },
            renderedPages() {
                let fromPage    = this.pagination.currentPage - this.eachSide
                let toPage      = this.pagination.currentPage + this.eachSide
                let filterdPages= this.pages.filter(page => page >= fromPage && page <= toPage);
                return filterdPages
            }
        }
    }
</script>

<style scoped lang="scss">
    .pagination {
        justify-content: flex-end !important;
        .page-stats {
            align-items: center;
            margin-right: 5px;
            margin-top: 2px;
        }
        i {
            color: #3273dc !important;
        }
    } 
</style>