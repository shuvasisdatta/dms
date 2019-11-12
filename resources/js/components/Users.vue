<template>
     <div class="row justify-content-center">
         <div class="col-12">
             <div class="card">
               <div class="card-header form-inline">
                   <div class="float-left form-inline">
                       <label class="font-weight-normal">Show</label>
                        <select class="custom-select ml-2" v-model="tableData.perPage" @change="$emit('perPageOptionChanged')">
                            <option v-for="pageNo in perPageDropDown" :key="pageNo.id" :value="pageNo">{{ pageNo }}</option>
                        </select>
                   </div>
                   <div style="margin:0 auto"><h3>Users</h3></div>
                   <div class="card-tools float-right">
                        <button class="btn btn-default mb-2 mr-sm-2 bg-green"  
                            :disabled="Object.entries(tableData.search).length === 0 && tableData.search.constructor === Object"
                            data-toggle="tooltip" title="Search"
                            @click="$emit('searched')"><i class="fas fa-search"></i>&nbsp; Search</button>
                        <button class="btn btn-default mb-2 mr-sm-2 bg-red"
                            :disabled="Object.entries(tableData.search).length === 0 && tableData.search.constructor === Object"  
                            data-toggle="tooltip" title="Reset Search"
                            @click="$emit('searchReset')"><i class="fas fa-power-off"></i>&nbsp; Reset Search</button>
                        <button type="submit" class="btn btn-primary mb-2" @click="createModal(); viewDocument = false; documentSrc='';">Add New</button>
                    </div>
               </div>
               <!-- /.card-header -->
               <div class="card-body table-responsive">
                    <app-data-table :columns="columns" 
                        :sortKey="sortKey" 
                        :sortOrders="sortOrders"
                        @sort="sortBy">
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" 
                                        class="form-control" 
                                        placeholder="Filter name"
                                        v-model="tableData.search.name"
                                        @keyup.enter="$emit('columnFiltered', 'name')" />
                                </td>
                                <td>
                                    <input type="text" 
                                        class="form-control" 
                                        placeholder="Filter email"
                                        v-model="tableData.search.email"
                                        @keyup.enter="$emit('columnFiltered', 'email')" />
                                </td>
                                <td>
                                    <v-select class="bg-white" style="min-width: 100px;"
                                        v-model="tableData.search.department_id"
                                        @search:focus="getRelatedResources"
                                        :options="departments"
                                        label="name"
                                        :reduce="name => name.id"
                                        placeholder="Select">
                                        </v-select>
                                </td>
                                <td>
                                    <v-select class="bg-white" style="min-width: 100px;"
                                        v-model="tableData.search.role_id"
                                        @search:focus="getRelatedResources"
                                        @input="$emit('plantSelected', tableData.search.role_id)"
                                        :options="roles"
                                        label="name"
                                        :reduce="name => name.id"
                                        placeholder="Select">
                                        </v-select>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.department.name }}</td>
                                <td>{{ user.role.name }}</td>
                                <td>{{ user.created_at }}</td>
                                <td>{{ user.updated_at }}</td>
                                <td>
                                    <button class="btn btn-success" @click="editModal(user)"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger" @click="destroyData(user.id)"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </app-data-table>

                    <app-pagination :pagination="pagination" @pageClicked="(page_url) => $emit('pageChanged', page_url)"></app-pagination>
               </div>
               <!-- /.card-body -->
             </div>
             <!-- /.card -->
         </div>
         <!-- /.col-12 -->
         <!-- Modal -->
        <div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editMode" class="modal-title" id="ModalTitle">Add User</h5>
                        <h5 v-show="editMode" class="modal-title" id="ModalTitle">Update User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form @submit.prevent="editMode? updateData() : createData()" @keydown="form.onKeydown($event)">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Name</label>
                                <input v-model="form.name" type="text" name="name" placeholder="Name"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                <has-error :form="form" field="name"></has-error>
                            </div>
                            
                            <div class="form-group">
                                <label>E-mail</label>
                                <input v-model="form.email" type="email" name="email" placeholder="E-mail"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                                <has-error :form="form" field="email"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Password</label>
                                <input v-model="form.password" type="password" name="password" placeholder="Password"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                                <has-error :form="form" field="password"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Role</label>
                                <v-select 
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('role_id') }"
                                    v-model="form.role_id"
                                    :options="roles"
                                    label="name"
                                    :reduce="name => name.id"
                                    placeholder="Select">
                                </v-select>
                                <has-error :form="form" field="role_id"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Department</label>
                                <v-select 
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('department_id') }"
                                    v-model="form.department_id"
                                    :options="departments"
                                    label="name"
                                    :reduce="name => name.id"
                                    placeholder="Select">
                                </v-select>
                                <has-error :form="form" field="department_id"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
                            <button v-show="editMode" type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ./Modal -->
    </div>
</template>

<script>
    import DataTable from "./helper/DataTable"
    import Pagination from "./helper/Pagination"
    import vSelect from 'vue-select'
    import 'vue-select/dist/vue-select.css';

    export default {
        components: {
            appDataTable: DataTable,
            appPagination: Pagination,
            'v-select': vSelect
        },
        data() {
            let sortOrders = [];
            let columns = [
                {name: 'name', label: 'Name', sortable: true, filterable: true },
                {name: 'email', label: 'Email', sortable: true, filterable: true },
                {name: 'department.name', label: 'Department', sortable: false, filterable: true },
                {name: 'role.name', label: 'Role', sortable: false, filterable: true },
                {name: 'created_at', label: 'Created', sortable: true},
                {name: 'updated_at', label: 'Updated', sortable: true},
                {name: 'modify', label: 'Modify', sortable: false, filterable: false},
            ];

            columns.forEach((column) => {
                sortOrders[column.name] = -1;
            });

            return {
                columns: columns,
                sortKey: 'created_at',
                sortOrders: sortOrders,
                tableData: {
                    draw: 0,
                    perPage: 5,
                    search: {},
                    sortColumn: 'created_at',
                    sortDir: 'desc'
                },
                pagination: {
                    lastPage: '',
                    currentPage: '',
                    total: '',
                    lastPageUrl: '',
                    nextPageUrl: '',
                    prevPageUrl: '',
                    from: '',
                    to: '',
                },
                perPageDropDown: [5, 10, 15],
                editMode: false,
                api_url: "api/user/",
                users: [],
                departments: [],
                roles: [],
                form: new Form( {
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    role_id: '',
                    department_id: '',
                }),
            }
        },
        computed: {
            // get filterable columns from table
            filterableColumns: function() {
                return this.columns.filter( function(column) {
                    return column.filterable;
                });
            },
        },
        watch: {
        },
        methods: {
            configPagination(data) {
                this.pagination.lastPage = data.last_page;
                this.pagination.currentPage = data.current_page;
                this.pagination.total = data.total;
                this.pagination.lastPageUrl = data.last_page_url;
                this.pagination.nextPageUrl = data.next_page_url;
                this.pagination.prevPageUrl = data.prev_page_url;
                this.pagination.from = data.from;
                this.pagination.to = data.to;
            },

            sortBy(key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
                this.tableData.sortColumn = key;
                this.tableData.sortDir = this.sortOrders[key] === 1? 'asc': 'desc';
                
                // emit an event to refetch data to update table
                this.$emit('sorted');
            },

            createModal() {
                this.editMode = false;
                this.form.clear();
                this.form.reset();

                this.getRelatedResources();

                $('#Modal').modal('show');
            },

            editModal(data) {
                this.editMode = true;
                this.form.clear();
                this.form.reset();
                $('#Modal').modal('show');

                this.getRelatedResources();

                this.form.fill(data);
            },
            
            getUsers(url = this.api_url) {   
                this.tableData.draw++;
                axios.get(url, {params: this.tableData})
                    .then(response => {
                        let data = response.data;
                        if(this.tableData.draw == data.draw) {
                            this.users = data.data.data;
                            this.configPagination(data.data);
                        }
                        this.$Progress.finish(); 
                    })
                    .catch(errors => {
                        Swal.fire(
                            'Failed!',
                            'Document Loading has been failed',
                            'warning'
                        );
                        this.$Progress.fail();
                    });
            },

            getRelatedResources() {
                this.$Progress.start()
                this.form.get('api/fetchUserRelatedModels')
                .then(({data}) => {
                    this.departments    = data.departments;
                    this.roles          = data.roles;

                    this.$Progress.finish(); 
                })
                .catch(() => {
                    Swal.fire(
                        'Failed!',
                        'Information Loading has been failed',
                        'warning'
                    );
                    this.$Progress.fail();
                });
            },

            createData() {
                this.$Progress.start();
                
                this.form.post(this.api_url).then(() => {
                    $('#Modal').modal('hide');
                    
                    toast.fire({
                        title: 'Information created successfully',
                        type: 'success'
                    });

                    // emit an event to refetch data to update table
                    this.$emit('created');

                    this.$Progress.finish();
                })
                .catch((response) => {
                    toast.fire({
                        title: 'Information creating failed!' + response,
                        type: 'error'
                    });

                    this.$Progress.fail();
                });

                this.$Progress.finish();
            },

            updateData() {
                this.$Progress.start()
                this.form.put(this.api_url + this.form.id)
                .then(() => {
                    $('#Modal').modal('hide');
                    toast.fire({
                        title: 'Information updated successfully',
                        type: 'success'
                    });

                    // emit an event to refetch data to update table
                    this.$emit('updated');

                    this.$Progress.finish();
                })
                .catch(() => {
                    toast.fire({
                        title: 'Information updating failed!',
                        type: 'error'
                    });
                    this.$Progress.fail();
                });
            },

            destroyData (id) {
                this.$Progress.start();
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios.delete(this.api_url + id)
                        .then(() => {
                            toast.fire({
                                title: 'Deleted successfully',
                                type: 'success'
                            });
                            
                            // emit an event to refetch data to update table
                            this.$emit('deleted');

                            this.$Progress.finish();
                        })
                        .catch(() => {
                            Swal.fire(
                                'Failed!',
                                'Something has been wrong',
                                'warning'
                            );
                            this.$Progress.fail();
                        });
                    }
                });        
            },
        },
        created() {
            this.getUsers();

            // listen to events
            this.$on('created', () => {
                this.getUsers();
            })

            this.$on('updated', () => {
                this.tableData.sortColumn = 'updated_at'
                this.getUsers()
            })

            this.$on('deleted', () => {
                this.getUsers()
            })

            this.$on('sorted', () => {
                this.getUsers()
            })

            this.$on('searched', () => {
                this.getUsers()
            })

            this.$on('searchReset', () => {
                this.tableData.search = {};
                this.getUsers()
            })

            this.$on('perPageOptionChanged', () => {
                this.getUsers()
            })

            this.$on('pageChanged', (page_url) => {
                this.getUsers(page_url)
            })                   
        }
    }
</script>

<style scoped>
    div.v-select.form-control {
        display: inline-table;
    }
</style>