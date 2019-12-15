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
                   <div style="margin:0 auto"><h3>Documents</h3></div>
                   <div class="card-tools float-right">
                        <button class="btn btn-default mb-2 mr-sm-2 bg-green"  
                            :disabled="Object.entries(tableData.search).length === 0 && tableData.search.constructor === Object"
                            data-toggle="tooltip" title="Search"
                            @click="$emit('searched')"><i class="fas fa-search"></i>&nbsp; Search</button>
                        <button class="btn btn-default mb-2 mr-sm-2 bg-red"
                            :disabled="Object.entries(tableData.search).length === 0 && tableData.search.constructor === Object"  
                            data-toggle="tooltip" title="Reset Search"
                            @click="$emit('searchReset')"><i class="fas fa-power-off"></i>&nbsp; Reset Search</button>
                        <button type="submit" class="btn btn-primary mb-2" v-if="$parent.userRole === 'Admin'" @click="createModal(); viewDocument = false; documentSrc='';">Add New</button>
                    </div>
               </div>
               <!-- /.card-header -->
               <div class="card-body table-responsive">
                    <app-data-table :columns="columns" 
                        :sortKey="sortKey" 
                        :sortOrders="sortOrders"
                        :columnFilter=true
                        @sort="sortBy">
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" 
                                        class="form-control" 
                                        name="title" 
                                        placeholder="Filter title"
                                        v-model="tableData.search.title"
                                        @keyup.enter="$emit('columnFiltered', 'title')" />
                                </td>
                                <td>
                                    <input type="text" 
                                        class="form-control" 
                                        name="description" 
                                        placeholder="Filter description"
                                        v-model="tableData.search.description"
                                        @keyup.enter="$emit('columnFiltered', 'description')" />
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
                                    <v-select class="bg-white" style="min-width: 120px;" 
                                        v-model="tableData.search.plant_id"
                                        @search:focus="getRelatedResources"
                                        @input="$emit('plantSelected', tableData.search.plant_id, 'filter')"
                                        :options="plants"
                                        label="name"
                                        :reduce="name => name.id"
                                        placeholder="Select">
                                        </v-select>
                                </td>
                                <td>
                                    <v-select class="bg-white" style="min-width: 120px;"
                                        v-model="tableData.search.equipment_id"
                                        @search:focus="getRelatedResources()"
                                        :options="equipments"
                                        label="name"
                                        :reduce="name => name.id"
                                        placeholder="Select">
                                        </v-select>
                                </td>
                                <td>
                                    <v-select class="bg-white" style="min-width: 100px;"
                                        v-model="tableData.search.category_id"
                                        @search:focus="getRelatedResources"
                                        :options="categories"
                                        label="name"
                                        :reduce="name => name.id"
                                        placeholder="Select">
                                        </v-select>
                                </td>
                                <td>
                                    <v-select class="bg-white" style="min-width: 100px;"
                                        v-model="tableData.search.locker_id"
                                        @search:focus="getRelatedResources"
                                        :options="lockers"
                                        label="name"
                                        :reduce="name => name.id"
                                        placeholder="Select">
                                        </v-select>
                                </td>
                                <td></td>
                                <td></td>
                                <td v-if="$parent.userRole === 'Admin'"></td>
                            </tr>
                            <!-- viewDocument = true; documentSrc = document.slug.replace('public/', 'storage/');  -->
                            <tr v-for="document in documents" :key="document.id">
                                <td v-if="documentAllowedToEmbed.includes(document.type)">
                                    <a href="#" @click="documentModalShow(document);">
                                        {{ document.title }}
                                    </a>
                                </td>
                                <td v-else>
                                    <a :href="document.slug.replace('public/', 'storage/')" download>
                                        {{ document.title }}
                                    </a>
                                </td>
                                <td>{{ document.description }}</td>
                                <td>{{ document.department.name }}</td>
                                <td>{{ document.plant.name }}</td>
                                <td>{{ document.equipment.name }}</td>
                                <td>{{ document.category.name }}</td>
                                <td>{{ document.locker.name }}</td>
                                <td>
                                    <i class="red fas fa-file fa-3x"
                                        :class="{'fa-file-pdf': documentTypePdf.includes(document.type), 
                                            'fa-file-powerpoint': documentTypePowerpoint.includes(document.type),
                                            'fa-file-word': documentTypeWord.includes(document.type),
                                            'fa-file-excel': documentTypeExcel.includes(document.type),
                                            'fa-file-video': documentTypeVideo.includes(document.type),
                                            'fa-file-audio': documentTypeMusic.includes(document.type),
                                            'fa-file-archive': documentTypeArchive.includes(document.type),
                                            'fa-file-image': documentTypeImage.includes(document.type)}"
                                        :title="document.type">
                                    </i>
                                </td>
                                <td>{{ document.created_at }}</td>
                                <td v-if="$parent.userRole === 'Admin'">
                                    <button class="btn btn-success" @click="editModal(document);"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-danger" @click="destroyData(document.id);"><i class="fas fa-trash"></i></button>
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
                         <h5 v-show="!editMode" class="modal-title" id="ModalTitle">Add Document</h5>
                         <h5 v-show="editMode" class="modal-title" id="ModalTitle">Update Document</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
                     <form @submit.prevent="editMode? updateData() : createData()" @keydown="form.onKeydown($event)">
                         <div class="modal-body">
                             <div class="form-group">
                                 <label>Name</label>
                                 <input v-model="form.title" type="text" name="title" placeholder="Title"
                                     class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                 <has-error :form="form" field="title"></has-error>
                             </div>

                             <div class="form-group">
                                 <label>Description</label>
                                 <textarea v-model="form.description" name="description" placeholder="Description"
                                     class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                                 </textarea>
                                 <has-error :form="form" field="description"></has-error>
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

                             <div class="form-group">
                                 <label>Plant</label>
                                 <v-select 
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('plant_id') }"
                                    v-model="form.plant_id"
                                    @input="editMode? $emit('plantSelected', form.plant_id, 'edit'): $emit('plantSelected', form.plant_id)"
                                    :options="plants"
                                    label="name"
                                    :reduce="name => name.id"
                                    placeholder="Select">
                                </v-select>
                                <has-error :form="form" field="plant_id"></has-error>
                             </div>

                             <div class="form-group">
                                 <label>Equipment</label>
                                 <v-select 
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('equipment_id') }"
                                    v-model="form.equipment_id"
                                    :options="equipments"
                                    label="name"
                                    :reduce="name => name.id"
                                    placeholder="Select">
                                </v-select>
                                 <has-error :form="form" field="equipment_id"></has-error>
                             </div>

                             <div class="form-group">
                                 <label>Category</label>
                                 <v-select 
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('category_id') }"
                                    v-model="form.category_id"
                                    :options="categories"
                                    label="name"
                                    :reduce="name => name.id"
                                    placeholder="Select">
                                </v-select>
                                 <has-error :form="form" field="category_id"></has-error>
                             </div>

                             <div class="form-group">
                                 <label>Locker</label>
                                 <v-select 
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('locker_id') }"
                                    v-model="form.locker_id"
                                    :options="lockers"
                                    label="name"
                                    :reduce="name => name.id"
                                    placeholder="Select">
                                </v-select>
                                 <has-error :form="form" field="locker_id"></has-error>
                             </div>

                            <!-- <div class="form-group" v-if="editMode">
                                <label>User</label>
                                <select v-model="form.user_id" type="text" name="user_id"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('user_id') }">
                                    <option value="">Select Created By User</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                 </select>
                                 <has-error :form="form" field="user_id"></has-error>
                            </div> -->
                             
                             <div class="form-group" v-if="!editMode">
                                <label>Document</label>
                                <div class="input-group bg-secondary">
                                 <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.has('document') }"
                                     readonly v-model="form.document.name" placeholder="Select a file">
                                 <span class="input-group-btn">
                                    <button class="btn btn-default bg-primary" type="button" @click="showFilePicker">
                                        <i class="fas fa-paperclip"></i>
                                    </button>
                                 </span>
                                </div>
                                <input type="file" name="document" @change="fileChanged" ref="document" class="form-control" :class="{ 'is-invalid': form.errors.has('document') }" style="display:none;">
                                <has-error :form="form" field="document"></has-error>
                                <br/>
                                <div class="progress"  v-if="fileUploadPercentage !== 0">
                                    <div class="progress-bar" id="uploadProgressBar" role="progressbar" :style="{ width: fileUploadPercentage + '%' }" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{ fileUploadPercentage }}%</div>
                                </div>
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
         <div class="modal fade" data-backdrop="static" data-keyboard="true" id="DocumentModal" tabindex="-1" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true" @keydown="documentModalClose">
             <div class="modal-dialog modal-dialog-centered" role="document">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title" id="ModalTitle">Selected Document</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" @click="documentModalClose">&times;</span>
                         </button>
                     </div>
                     <div class="modal-body">
                        <!-- Display PDFViewer if Selected Document is PDF -->
                        <appPDFViewer v-if="documentSrc" :src="documentSrc"></appPDFViewer>
                     </div>
                 </div>
             </div>
         </div>
         <!-- ./Modal -->
     </div>
 </template>
  
 <script>
    import DataTable from "./helper/DataTable"
    import Pagination from "./helper/Pagination"
    import PDFViewer from "./PDFViewer"
    import vSelect from 'vue-select'
    import 'vue-select/dist/vue-select.css';
    import objectToFormData from '../objectToFormData.js'

    export default {
        components: {
            appDataTable: DataTable,
            appPagination: Pagination,
            appPDFViewer: PDFViewer,
            'v-select': vSelect
        },
        data() {
            let sortOrders = [];
            let columns = [
                {name: 'title', label: 'Title', sortable: true, filterable: true},
                {name: 'description', label: 'Description', sortable: true, filterable: true},
                {name: 'department.name', label: 'Department', sortable: true, filterable: true},
                {name: 'plant.name', label: 'Plant', sortable: true, filterable: true},
                {name: 'equipment.name', label: 'Equipment', sortable: true, filterable: true},
                {name: 'category.name', label: 'Category', sortable: true, filterable: true},
                {name: 'locker.name', label: 'Locker', sortable: true, filterable: true},
                {name: 'type', label: 'Type', sortable: false},
                {name: 'created_at', label: 'Created', sortable: true, filterable: false}
            ];

            if(this.$parent.userRole === 'Admin') {
                columns.push({name: 'modify', label: 'Modify', sortable: false, filterable: false})
            } 

            
            columns.forEach((column) => {
                sortOrders[column.name] = -1;
            })

            return {
                fileUploadPercentage: 0,
                documentSrc: '',
                documentTypeMusic: ['mp3'],
                documentTypeArchive: ['zip', 'rar', 'tar', '7z'],
                documentTypeVideo: ['mp4', 'wmv', 'webm', 'mkv', '3gp'],
                documentTypeImage: ['png', 'jpg', 'jpeg'],
                documentTypePdf: ['pdf'],
                documentTypePowerpoint: ['ppt'],
                documentTypeWord: ['doc', 'docx'],
                documentTypeExcel: ['xlx', 'xlsx'],
                documentAllowedToEmbed: ['pdf', 'mp4'],
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
                api_url: '/api/document/',
                documents: [],
                departments: [],
                plants: [],
                equipments: [],
                categories: [],
                lockers: [],
                users: [],
                form: new Form( {
                    id: '',
                    title: '',
                    description: '',
                    department_id: '',
                    plant_id: '',
                    equipment_id: '',
                    category_id: '',
                    locker_id: '',
                    // user_id: '',
                    document: ''
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

            documentModalShow(document) {
                this.documentSrc = document.slug.replace('public/', 'storage/');
                $('#DocumentModal').modal('show');
            },

            documentModalClose() {
                this.documentSrc = undefined;
            },

            createModal() {
                this.editMode = false;
                this.form.clear();
                this.form.reset();
                
                this.fileUploadPercentage = 0;

                this.getRelatedResources();

                $('#Modal').modal('show');
            },

            editModal(data) {
                this.editMode = true;
                this.form.clear();
                this.form.reset();
                
                this.getRelatedResources('edit');
                this.form.fill(data);
                this.$emit('plantSelected', this.form.plant_id, 'edit')
                $('#Modal').modal('show');
            },
            
            getDocuments(url = this.api_url) {
                this.tableData.draw++;
                axios.get(url, {params: this.tableData})
                    .then(response => {
                        let data = response.data;
                        if(this.tableData.draw == data.draw) {
                            this.documents = data.data.data;
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
            
            getRelatedResources(action = 'create') {
                if(action !== 'edit') {
                    if(!this.tableData.search.plant_id) {
                        this.equipments = []
                        delete this.tableData.search.equipment_id
                    }
                }

                if(this.departments.length < 1 || this.plants.length < 1 || this.categories.length < 1 || this.lockers.length < 1) {
                    this.$Progress.start()
                    this.form.get('api/fetchDocumentRelatedModels')
                    .then(({data}) => {
                        this.departments    = data.departments;
                        this.plants         = data.plants;

                        if(action === 'edit') {
                            this.$emit('plantSelected', this.form.plant_id, action)
                        }

                        this.categories     = data.categories;
                        this.lockers        = data.lockers;

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
                }
                
            },

            showFilePicker() {
                this.$refs.document.click();
            },

            fileChanged(e) {
                let file = e.target.files[0];
                this.form.document = file;
            },

            createData() {
                this.$Progress.start();
                this.form.submit('post', this.api_url, {
                    transformRequest: [function (data, headers) {
                        return objectToFormData(data)
                    }],
                    onUploadProgress: e => {
                        // Do whatever you want with the progress event
                        if(this.form.document) {
                            let percentCompleted = Math.round(e.loaded/e.total * 100);
                            this.fileUploadPercentage = percentCompleted;
                        }
                    }
                }).then(() => {
                    $('#Modal').modal('hide');
                    
                    toast.fire({
                        title: 'Information created successfully',
                        type: 'success'
                    });

                    // emit an event to refetch data to update table
                    this.$emit('created');

                    this.$Progress.finish();
                })
                .catch((error) => {
                    console.log(error);
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
                this.form.put('api/document/'+this.form.id)
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
                        axios.delete('api/document/'+id)
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
            }
        },
        created() {
            this.getDocuments();

            // listen to events
            this.$on('created', () => {
                this.tableData.search = {}
                this.getDocuments();
            })

            this.$on('updated', () => {
                this.tableData.sortColumn = 'updated_at'
                this.tableData.search = {}
                this.getDocuments()
            })

            this.$on('deleted', () => {
                this.tableData.search = {}
                this.getDocuments()
            })

            this.$on('sorted', () => {
                this.getDocuments()
            })

            this.$on('searched', () => {
                this.getDocuments()
            })

            this.$on('searchReset', () => {
                this.tableData.search = {};
                this.getDocuments()
            })

            this.$on('perPageOptionChanged', () => {
                this.getDocuments()
            })

            this.$on('pageChanged', (page_url) => {
                this.getDocuments(page_url)
            })

            this.$on('plantSelected', (plant_id, action = 'create') => {
                console.log('plantSelected ' + plant_id + ' ' + action)
                if(action === 'create') {
                    this.form.equipment_id = ""
                } else if (action === 'filter') {
                    delete this.tableData.search.equipment_id
                }

                if(typeof(plant_id) == 'number') {
                    this.equipments = this.plants.find(x => x.id === plant_id)? this.plants.find(x => x.id === plant_id).equipments: []
                    console.log(this.equipments.length)
                    if(this.equipments.length > 0) {
                        this.form.equipment_id = this.plants.find(x => x.id === plant_id).equipments.find(x => x.id === this.form.equipment_id)? this.form.equipment_id: '';
                        console.log('equipment - ' + this.form.equipment_id)
                    }
                } else {
                    this.equipments = [];
                    this.tableData.search.equipment_id = null;
                    this.form.equipment_id = '';
                }
            })
        }
    }
</script>


<style scoped>
    #DocumentModal .modal-dialog {
        max-width: 90%;
    }
    div.v-select.form-control {
        display: inline-table;
    }
</style>