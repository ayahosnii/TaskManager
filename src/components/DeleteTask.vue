<template>
    <li class="list-group-item d-flex align-items-center ps-0 pe-3 py-1 rounded-0 border-0 bg-transparent">
        <div class="form-check">
            <a href="#!" class="text-danger" data-mdb-toggle="tooltip" title="Delete todo" @click="deleteModal">
                <i class="ti ti-trash"></i>
            </a>
        </div>
    </li>

    <div class="modal" v-if="deleteMode" style="display: block" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Task</h5>
                    <button type="button" @click="cancelChanges" class="close bg-transparent border-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete {{ taskTitle }} task?</p>
                </div>
                <div class="modal-footer">
                    <form @submit.prevent="deleteTask(taskId)" class="d-flex justify-content-center align-items-center mb-4">
                        <button type="button" class="btn btn-secondary mx-2" @click="cancelChanges" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Delete</button>
                    </form>
                </div>
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
    },
    data() {
        return {
            deleteMode: false,
        };
    },
    methods: {
        deleteTask(taskId) {
            try {
                const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

                console.log(csrfToken);
                console.log('Sending Payload:', { title: this.editedTaskTitle, description: this.editedTaskDescription });

                const responsePromise = axios.delete('api/tasks/' + taskId, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    },
                });

                responsePromise.then(response => {
                    console.log('Response:', response);
                    console.log('Response Data:', response.data);

                    $toast.success('Task created successfully');
                    // Emit the task-created event with the updated task data
                    this.$emit('task-deleted', this.taskId);

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
            this.deleteMode = false;
            this.$emit('save-changes');
        },
        cancelChanges() {
            this.deleteMode = false;
            this.$emit('cancel-changes', {title: this.editedTaskTitle});
        },

        deleteModal() {
            this.deleteMode = !this.deleteMode;
        },
    },
};
</script>

<style>
/* The Modal (background) */
.modal {
    background-color: rgba(71, 78, 93, 0.35);
}

</style>
