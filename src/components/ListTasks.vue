<template>
    <div class="row">
        <div :class="selectedTask ? 'col-md-9' : 'col-md-12'">
            <ul
                v-for="task in paginatedTasks"
                :key="task.id"
                class="list-group list-group-horizontal rounded-0 border m-2 p-2 bg-transparent"
            >
                <li class="list-group-item d-flex align-items-center ps-0 pe-3 py-1 rounded-0 border-0 bg-transparent">
                    <div class="form-check">
                        <input
                            class="form-check-input me-0"
                            type="checkbox"
                            :checked="task.completed === 1"
                            @change="toggleTaskCompletion(task)"
                            value=""
                            id="flexCheckChecked1"
                            aria-label="..."
                        />
                    </div>
                </li>
                <li class="list-group-item px-3 py-1 d-flex align-items-center flex-grow-1 border-0 bg-transparent">
                    <p class="lead fw-normal mb-0" @dblclick="showEditTask(task)">{{ task.title }}</p>
                </li>

                <EditTask
                    v-if="isEditTaskVisible && currentTaskId === task.id"
                    :task-id="task.id"
                    :task-title="task.title"
                    :task-description="task.description"
                    @task-created="handleTaskCreated"
                    @cancel-changes="handleCancelChanges"
                />

                <li class="list-group-item ps-3 pe-0 py-1 rounded-0 border-0 bg-transparent">
                    <div class="d-flex flex-row justify-content-end mb-1">
                        <a href="#!" class="text-info" @click="showTaskDetails(task)" title="Edit todo">
                            <i class="ti ti-arrow-bear-right"></i>
                        </a>
                    </div>
                    <div class="text-end text-muted">
                        <a href="#!" class="text-muted" data-mdb-toggle="tooltip" title="Created date">
                            <p class="small mb-0"><i class="fas fa-info-circle me-2"></i>{{ new Date(task.created_at).toLocaleDateString() }}</p>
                        </a>
                    </div>
                </li>
            </ul>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <a class="page-link" href="#" @click.prevent="prevPage">&laquo;</a>
                    </li>
                    <li class="page-item" v-for="page in pages" :key="page" :class="{ active: currentPage === page }">
                        <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <a class="page-link" href="#" @click.prevent="nextPage">&raquo;</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div v-if="selectedTask" class="col-md-3">
            <div class="card">
                <ul class="list-group list-group-horizontal rounded-0 border m-2 p-2 bg-transparent">
                    <li class="list-group-item d-flex align-items-center ps-0 pe-3 py-1 rounded-0 border-0 bg-transparent">
                        <div class="form-check">
                            <input
                                class="form-check-input me-0"
                                type="checkbox"
                                :checked="selectedTask.completed === 1"
                                @change="toggleTaskCompletion(selectedTask)"
                            />
                        </div>
                    </li>
                    <li class="list-group-item px-3 py-1 d-flex align-items-center flex-grow-1 border-0 bg-transparent">
                        <p class="lead fw-normal mb-4">{{ selectedTask.title }}</p>
                    </li>
                </ul>
                <div class="form-control border-0">
                    <textarea rows="5" class="border" v-model="selectedTask.description"></textarea>
                </div>
                <div class="form-control border-0">
                    <ul class="list-group list-group-horizontal rounded-0 border m-2 p-2 bg-transparent">
                        <DeleteTask
                            :task-id="selectedTask.id"
                            :task-title="selectedTask.title"
                            @task-deleted="handleTaskDeleted"
                        />
                        <li class="list-group-item ps-3 pe-0 py-1 rounded-0 border-0 bg-transparent">
                            <div class="d-flex flex-row justify-content-end mb-1">
                                <a href="#!" class="text-info" @click="showTaskDetails(selectedTask)" title="Edit todo">
                                    <i class="ti ti-arrow-bear-right"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
// Import Vue component dependencies
import EditTask from './EditTask.vue';
import DeleteTask from "./DeleteTask.vue";
import axios from 'axios';

export default {
    // Define component properties
    props: {
        tasks: Array,
        showCompletedTasks: Boolean,
        showTodayTasks: Boolean,
    },
    // Register child components
    components: {
        DeleteTask,
        EditTask,
    },

    mounted() {
        console.log('Completed Tasks:', this.showCompletedTasks)
        console.log('Today Tasks:', this.showTodayTasks)
    },
    // Data properties
    data() {
        return {
            isEditTaskVisible: false,
            currentTaskId: null,
            selectedTask: null,
            itemsPerPage: 4,
            currentPage: 1,
        };
    },
    // Computed properties
    computed: {
        // Filter tasks based on user preferences
        filteredTasks() {
            return this.tasks.filter(task => {
                const todayTask = this.isTodayTask(task);
                const completedTask = task.completed === 1;

                if (this.showTodayTasks && this.showCompletedTasks) {
                    return todayTask && completedTask;
                } else if (!this.showTodayTasks && this.showCompletedTasks) {
                    return completedTask;
                } else if (this.showTodayTasks && !this.showCompletedTasks) {
                    return todayTask && !completedTask;
                } else {
                    return !completedTask;
                }
            });
        },
        // Paginate tasks based on current page and items per page
        paginatedTasks() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.filteredTasks.slice(startIndex, endIndex);
        },
        // Calculate total number of pages
        totalPages() {
            return Math.ceil(this.filteredTasks.length / this.itemsPerPage);
        },
        // Generate an array representing page numbers
        pages() {
            return Array.from({ length: this.totalPages }, (_, index) => index + 1);
        },
    },

    methods: {
        // Check if a task is created today
        isTodayTask(task) {
            console.log('methods ' + this.showTodayTasks);
            if (this.showTodayTasks === 0) {
                console.log(this.showTodayTasks);
                return true;
            }

            const taskDate = new Date(task.created_at);
            const today = new Date();

            return (
                taskDate.getFullYear() === today.getFullYear() &&
                taskDate.getMonth() === today.getMonth() &&
                taskDate.getDate() === today.getDate()
            );
        },
        // Toggle the completion status of a task
        markTaskAsCompleted(task) {
            // Create a copy of tasks before modifying
            const updatedTasks = [...this.tasks];
            const index = updatedTasks.findIndex(t => t.id === task.id);
            if (index !== -1) {
                updatedTasks[index].completed = !task.completed;
                this.$emit('fetch-completed-tasks');
                console.log(this.showCompletedTasks);

                // Update the tasks prop
                this.$emit('update:tasks', updatedTasks);
            }
        },
        // Show or hide task details
        showTaskDetails(task) {
            this.selectedTask = this.selectedTask === task ? null : task;
        },
        // Show the edit task form
        showEditTask(task) {
            this.isEditTaskVisible = true;
            this.currentTaskId = task.id;
        },
        // Hide the task form
        showTask() {
            this.isEditTaskVisible = false;
            this.currentTaskId = null;
        },
        // Handle canceling changes in the task form
        handleCancelChanges(data) {
            console.log('Canceled changes:', data);
            this.showTask();
        },
        // Handle task creation or update
        handleTaskCreated(updatedTask) {
            const index = this.tasks.findIndex(task => task.id === updatedTask.id);
            const tasks = this.tasks
            if (index !== -1) {
                tasks[index] = updatedTask;
            }
            this.showTask();
        },
        // Handle task deletion
        handleTaskDeleted(deletedTaskId) {
            const index = this.tasks.findIndex(task => task.id === deletedTaskId);
            const tasks = this.tasks
            if (index !== -1) {
                tasks.splice(index, 1);
                this.selectedTask = null;
            }
            this.toast.success('Task deleted successfully');
        },
        // Toggle completion status using API
        toggleTaskCompletion(task) {
            const csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
            const newCompletionStatus = task.completed === 1 ? 0 : 1;

            const responsePromise = axios.put(`/api/tasks/${task.id}/update-completion`, {
                completed: newCompletionStatus,
            }, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
            });

            responsePromise
                .then(response => {
                    console.log('Response:', response);
                    console.log('Response Data:', response.data);
                    const tasks = this.tasks
                    this.playCompletionSound();
                    this.selectedTask = null;

                    const index = this.tasks.findIndex(t => t.id === task.id);
                    if (index !== -1) {
                        tasks.splice(index, 1);
                    }
                })
                .catch(error => {
                    console.error('Error:', error.message);
                });
        },
        // Play a sound when a task is completed
        playCompletionSound() {
            const soundPath = '/assets/sounds/ping.mp3';
            const audio = new Audio(soundPath);
            audio.play();
        },
        // Change the current page
        changePage(page) {
            this.currentPage = page;
        },
        // Go to the previous page
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        // Go to the next page
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
    },
};
</script>
