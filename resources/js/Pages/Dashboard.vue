<template>
    <app-layout>
        <template #header>
            <h2 class="h4 font-weight-bold">
                Dashboard
            </h2>
        </template>

        <template #sidebar>
          <li v-for="item in $page['props']['menu']" class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" :href="menuRoute(item.route)">
              <i :class="item.icon" v-if="typeof item.icon != 'undefined'"></i>
              {{ item.title }}
            </a>
          </li>
        </template>

        <welcome />
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'

    export default {
        components: {
            AppLayout,
            Welcome,
        },
        methods: {
            menuRoute: function(route) {
                if(typeof route === 'object') {
                    return this.route(route.name, route.props);
                } else {
                    return this.route(route);
                }
            }
        }
    }
</script>
