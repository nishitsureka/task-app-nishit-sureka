<template>
  <div>
    <h2>Task Management Application</h2>

    <!-- Task Filter -->
    <div style="margin-bottom: 20px;">
      <label>Filter by Status:</label>
      <select v-model="filterStatus" @change="fetchTasks(1)">
        <option value="">All</option>
        <option value="pending">Pending</option>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>
      </select>
    </div>

    <div style="margin-bottom: 20px;">
      <input v-model="title" placeholder="Title (required)" />
      <input v-model="description" placeholder="Description (optional)" />
      <input type="date" v-model="due_date" placeholder="Due Date (optional)" />

      <select v-model="status">
        <option value="pending">Pending</option>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>
      </select>

      <button @click="createTask">Add Task</button>
    </div>

    <ul>
      <li v-for="task in tasks" :key="task.id" style="margin-bottom: 10px;">
        <strong>{{ task.title }}</strong> — {{ task.status }}

        <select v-model="task.status" @change="updateTask(task)">
          <option value="pending">Pending</option>
          <option value="in_progress">In Progress</option>
          <option value="completed">Completed</option>
        </select>

        <button @click="viewTask(task.id)">View</button>
        <button @click="deleteTask(task.id)">Delete</button>
      </li>
    </ul>

    <div v-if="pagination.total > pagination.per_page" style="margin-top: 20px;">
      <button 
        :disabled="pagination.current_page <= 1" 
        @click="fetchTasks(pagination.current_page - 1)">
        Previous
      </button>

      <span v-for="n in pagination.last_page" :key="n">
        <button 
          :disabled="pagination.current_page === n" 
          @click="fetchTasks(n)">
          {{ n }}
        </button>
      </span>

      <button 
        :disabled="pagination.current_page >= pagination.last_page" 
        @click="fetchTasks(pagination.current_page + 1)">
        Next
      </button>
    </div>

    <div v-if="selectedTask" style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px;">
      <h3>Task Details:</h3>
      <p><strong>Title:</strong> {{ selectedTask.title }}</p>
      <p><strong>Description:</strong> {{ selectedTask.description }}</p>
      <p><strong>Status:</strong> {{ selectedTask.status }}</p>
      <p><strong>Due Date:</strong> {{ selectedTask.due_date }}</p>
      <button @click="selectedTask = null">Close</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      tasks: [],
      selectedTask: null,
      title: '',
      description: '',
      status: 'pending',
      due_date: '',
      filterStatus: '',
      pagination: {},
    };
  },
  mounted() {
    this.fetchTasks();
  },
  methods: {
    fetchTasks(page = 1) {
      page = page < 1 ? 1 : page;

      axios.get('/api/tasks', {
        params: { status: this.filterStatus, page: page }
      })
      .then(res => {
        this.tasks = res.data.data;
        this.pagination = res.data.meta;
      })
      .catch(err => console.error(err));
    },


    createTask() {
      if (!this.title) return alert("Title is required");

      axios.post('/api/tasks', {
        title: this.title,
        description: this.description || null,
        status: this.status,
        due_date: this.due_date || null,
      })
      .then(() => {
        this.title = '';
        this.description = '';
        this.status = 'pending';
        this.due_date = '';
        this.fetchTasks(1);
      })
      .catch(err => console.error(err));
    },

    updateTask(task) {
      axios.put(`/api/tasks/${task.id}`, task)
        .catch(err => console.error(err));
    },


    deleteTask(id) {
      if (!confirm("Are  you sure you want to delete this task?")) return;

      axios.delete(`/api/tasks/${id}`)
        .then(() => this.fetchTasks(this.pagination.current_page))
        .catch(err => console.error(err));
    },
    viewTask(id) {
      axios.get(`/api/tasks/${id}`)
        .then(res => this.selectedTask = res.data.data)
        .catch(err => console.error(err));
    }
  }
};
</script>