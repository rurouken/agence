<template>
    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <router-link to="/" class="nav-link">Dashboard</router-link>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
import axios from "axios";
export default {
    name: 'Navbar',
}
</script>
