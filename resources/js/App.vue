<template>
    <layout-component
        @toggle-completed-tasks="handleToggleCompletedTasks"
        @toggle-today-tasks="handleToggleTodayTasks"
    >
        <div>
            <section>
                <div class="main-task">
                    <div class="card-body p-5">
                        <h6 class="mb-3">Today</h6>
                        <AddTask @task-created="handleTaskCreated"/>
                        <div class="d-flex align-items-center">
                            <li class="ti ti-pencil"></li>
                            <h6 class="text text-primary mb-0 ml-2">Double click on the word you want to edit</h6>
                        </div>
                        <ListTasks
                            :tasks="tasks"
                            :show-completed-tasks="showCompletedTasks"
                            :show-today-tasks="showTodayTasks"
                            @edit-task="editTask"
                            @mark-as-completed="markTaskAsCompleted"
                        />
                    </div>
                </div>
            </section>
        </div>
    </layout-component>
</template>

<style scoped>
section {
    padding: 5px;
}
</style>

<script>
import axios from 'axios';

import ListTasks from './components/ListTasks.vue';
import AddTask from './components/AddTask.vue';

export default {
    components: {
        ListTasks,
        AddTask,
    },
    data() {
        return {
            tasks: [],
            editedTask: null,
            showCompletedTasks: false,
            showTodayTasks: false,
        };
    },
    mounted() {
        this.fetchTasks();
    },
    methods: {
        editTask(task) {
            this.editedTask = task;
        },
        markTaskAsCompleted(task) {
            task.completed = !task.completed;
        },
        addTask(newTask) {
            this.tasks.push({id: this.tasks.length + 1, ...newTask});
        },
        saveTaskChanges(updatedTask) {
            const index = this.tasks.findIndex((task) => task.id === updatedTask.id);
            if (index !== -1) {
                this.tasks.splice(index, 1, updatedTask);
                this.editedTask = null; // Clear the edited task after saving changes
            }
        },
        async handleTaskCreated(taskData) {
            console.log('Task Created:', taskData);

            // Assuming taskData is an object with title and description properties
            const newTask = {id: this.tasks.length + 1, ...taskData};
            this.tasks.push(newTask);
            await this.fetchTasks();
        },
        async fetchTasks() {
            try {
                const response = await axios.get('/api/tasks');
                this.tasks = response.data;
                console.log(response.data)
            } catch (error) {
                console.error('Error fetching tasks:', error.message);
            }
        },

        handleToggleCompletedTasks(showCompletedTasks) {
            console.log('Toggle Completed Tasks:', showCompletedTasks);
            this.showCompletedTasks = showCompletedTasks;
        },

        handleToggleTodayTasks(showTodayTasks) {
            console.log('Toggle Today Tasks:', showTodayTasks);
            this.showTodayTasks = showTodayTasks;
        },
    },


};
</script>
