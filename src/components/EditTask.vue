<template>
    <div class="modal" style="display: block" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task {{this.taskId}}</h5>
                    <button type="button" @click="cancelChanges" class="close bg-transparent border-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="addTask(taskId)" class="d-flex justify-content-center align-items-center mb-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="modal-body">
                                <div class="row m-2">
                                    <input
                                        @dblclick="editMode = true"
                                        v-if="!editMode"
                                        class="form-control"
                                        v-model="editedTaskTitle" @blur="saveChanges" @keyup.enter="saveChanges" />
                                    <div v-if="validationError" class="text-danger">{{ validationError }}</div>

                                </div>
                                <div class="row m-2">
                                    <textarea v-model="editedTaskDescription" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="cancelChanges" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Edit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        taskId: Number,
        taskTitle: String,
        taskDescription: String,
    },
    data() {
        return {
            editMode: false,
            editedTaskTitle: this.taskTitle,
            editedTaskDescription: this.taskDescription,
            validationError: null,
        };
    },
    methods: {
        addTask(taskId) {
            if (!this.editedTaskTitle.trim()) {
                this.validationError = 'Title cannot be empty.';
                return;
            }

            this.validationError = null;

            try {
                const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

                console.log(csrfToken);
                console.log('Sending Payload:', { title: this.editedTaskTitle, description: this.editedTaskDescription });

                const responsePromise = axios.put('api/tasks/' + taskId, {
                    title: this.editedTaskTitle,
                    description: this.editedTaskDescription,
                }, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                });

                responsePromise.then(response => {
                    console.log('Response:', response);
                    console.log('Response Data:', response.data);

                    // Emit the task-created event with the updated task data
                    this.$emit('task-created', response.data);

                    // Close the modal and reset the form
                    this.cancelChanges();
                }).catch(error => {
                    console.error('Error:', error.message);
                });
            } catch (error) {
                console.error('Error:', error.message);
            }
        },

        saveChanges() {
            this.editMode = false;
            this.$emit('save-changes', {title: this.editedTaskTitle});
        },
        cancelChanges() {
            this.editMode = false;
            this.$emit('cancel-changes', {title: this.editedTaskTitle});
        },
    },
};
</script>

