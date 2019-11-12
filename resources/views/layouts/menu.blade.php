<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->

        <li class="nav-item" v-if="isAuthenticated && userRole === 'Admin'">
            <router-link to="/dashboard" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt teal"></i>
                <p>
                    Dashboard
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-if="isAuthenticated && userRole === 'Admin'">
            <router-link to="/roles" class="nav-link">
                <i class="nav-icon fas fa-circle indigo"></i>
                <p>
                    Roles
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-if="isAuthenticated && userRole === 'Admin'">
            <router-link to="/users" class="nav-link">
                <i class="nav-icon fas fa-users-cog blue"></i>
                <p>
                    Users
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-if="isAuthenticated && userRole === 'Admin'">
            <router-link to="/departments" class="nav-link">
                <i class="nav-icon fas fa-th orange"></i>
                <p>
                    Departments
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-if="isAuthenticated && userRole === 'Admin'">
            <router-link to="/plants" class="nav-link">
                <i class="nav-icon fas fa-th orange"></i>
                <p>
                    Plants
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-if="isAuthenticated && userRole === 'Admin'">
            <router-link to="/equipments" class="nav-link">
                <i class="nav-icon fas fa-th orange"></i>
                <p>
                    Equipments
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-if="isAuthenticated && userRole === 'Admin'">
            <router-link to="/categories" class="nav-link">
                <i class="nav-icon fas fa-th purple"></i>
                <p>
                    Categories
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-if="isAuthenticated && userRole === 'Admin'">
            <router-link to="/lockers" class="nav-link">
                <i class="nav-icon fas fa-th green"></i>
                <p>
                    Lockers
                </p>
            </router-link>
        </li>
        <li class="nav-item">
            <router-link to="/documents" class="nav-link">
                <i class="nav-icon fas fa-th teal"></i>
                <p>
                    Documents
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-if="!isAuthenticated">
            <router-link to="/login" class="nav-link">
                <i class="nav-icon fas fa-tools orange"></i>
                <p>
                    Login
                </p>
            </router-link>
        </li>
        <li class="nav-item" v-else>
            <a class="nav-link" href="#"
            {{-- <a class="nav-link" href="{{ route('logout') }}" --}}
                {{-- onclick="event.preventDefault(); document.getElementById('logout-form').submit();" --}}
                @click="$emit('onLogout')">
                <i class="nav-icon fas fa-sign-out-alt red"></i>
                <p>
                    {{ __('Logout') }}
                </p>
            </a>
            {{-- <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form> --}}
        </li>
    </ul>
</nav>
<!-- /.sidebar-menu -->