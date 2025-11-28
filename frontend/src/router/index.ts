import {createRouter,createWebHistory} from 'vue-router';
import Dashboard from '@/components/pages/Dashboard.vue'
import Login from '@/components/pages/Login.vue'
import AdminDashboard from '@/components/pages/admin/AdminDashboard.vue';
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
const routes=[{
    path:'/',
    component:DefaultLayout,
    children:[ {path:'/',name:'Home',component:Dashboard},
        {path:'/events',name:'Events',component:Event},
        {path:'/articles',name:'Articles'},
        {path:'/forums',name:'Forums'},
        {path:'/marketplace',name:'Marketplace'},
        {path:'/feedback',name:'Feedback'},
        {path:'/profile',name:'Profile'},
        {path:'/settings',name:'Settings'},
    ]
    },
    {
        path:'/login',
        component:AuthLayout,
        children:[
            { path:'/login',name:'Login',component:Login}
        ]

    },
    {
        path:'/admin',
        component:AdminLayout,
        children:[
            {path:'/admin',name:'admin',component:AdminDashboard}
        ]
    }
]

const router= createRouter({
    history: createWebHistory(),
    routes
})
export default router;