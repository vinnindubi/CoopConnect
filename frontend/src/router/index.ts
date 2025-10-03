import {createRouter,createWebHistory} from 'vue-router';
import Dashboard from '@/components/pages/Dashboard.vue'
import Login from '@/components/pages/Login.vue'
const routes=[
    {path:'/',name:'Home',component:Dashboard},
    {path:'/login', name:'Login', component:Login},
    // {path:'/events', name:'Login', component:Events},
    // {path:'/articles', name:'Login', component:Articles},
    // {path:'/forums', name:'Login', component:Forums},
    // {path:'/marketplace', name:'Login', component:Login},
    // {path:'/settings', name:'Login', component:Login},
    // {path:'/feedback', name:'Login', component:Login},
    // {path:'/profile', name:'Login', component:Login},
]

const router= createRouter({
    history: createWebHistory(),
    routes
})
export default router;