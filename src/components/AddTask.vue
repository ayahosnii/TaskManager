<template>
    <button @click="openCreateMode" class="btn btn-primary mb-5">Add a new task</button>

    <div v-if="createMode" class="modal" style="display: block" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create a task</h5>
                    <button type="button" @click="cancelChanges" class="close bg-transparent border-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="addTask" class="d-flex justify-content-center align-items-center mb-4">

                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 w-100 m-2">
                                <input v-model="title" placeholder="Title" type="text" id="form3" class="form-control form-control-lg" />
                                <!-- Display validation error message -->
                                <div v-if="!title.trim()" class="text-danger">Title cannot be empty.</div>

                                <textarea v-model="description" rows="6" placeholder="Description" class="form-control"></textarea>
                                <div v-if="description.length > 100" class="text-danger">Description cannot exceed 100 characters.</div>

                            </div>
                            <div class="col-md-12 w-100 m-2">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" @click="cancelChanges" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                </div>
                    </form>
                </div>
            </div>
        </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            title: '',
            description: '',
            createMode: false,
            validationError: null,
        };
    },
    methods: {
         addTask() {
             // Validate title
             if (!this.title.trim()) {
                 return;
             }

             // Validate description length
             if (this.description.length > 100) {
                 return;
             }

            try {
                const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

                console.log(csrfToken)
                    console.log('Sending Payload:', { title: this.title, description: this.description });

                const responsePromise  =  axios.post('api/tasks', {
                    title: this.title,
                    description: this.description,
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                        },
                });

                responsePromise.then(response => {
                    console.log('Response:', response);
                    console.log('Response Data:', response.data);

                    this.$emit('task-created', response.data);
                    this.cancelChanges()
                }).catch(error => {
                    console.error('Error:', error.message);
                });

            } catch (error) {
                console.error('Error:', error.message);
            }
        },
        cancelChanges() {
            this.createMode = false;
            this.$emit('cancel-changes');
        },
        openCreateMode() {
            this.createMode = true;
        },
    },
};
</script>
