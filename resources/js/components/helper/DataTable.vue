<template>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="column in columns" :key="column.name"
                        @click="column.sortable? $emit('sort', column.name): '';">
                        {{ column.label }}
                        <i class="float-right mt-1" 
                        :class="sortKey === column.name? (sortOrders[column.name] > 0? 'sorted-asc fas fa-chevron-up': 'sorted-desc fas fa-chevron-down'): (column.sortable? 'fas fa-sort': '')"></i>
                    </th>
                </tr>
            </thead>
            <slot></slot>
        </table>
    </div>
</template>

<script>
    import vSelect from 'vue-select'
    import 'vue-select/dist/vue-select.css';
    
    export default {
        components: {
            'v-select': vSelect
        },
        props: {
            columns: { 
                type: Array,
                required: true,
            },
            sortKey: { 
                type: String,
                required: true,
            },
            sortOrders: { 
                type: Array,
                required: true,
            },
            columnFilter: { 
                type: Boolean,
                default: false,
                required: false
            }
        },
    }
</script>

<style scoped>

</style>