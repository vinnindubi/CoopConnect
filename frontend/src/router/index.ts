import {createRouter,createWebHistory} from 'vue-router';
import Dashboard from '@/components/pages/Dashboard.vue'
import Login from '@/components/pages/Login.vue'
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue';
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

    }
]

const router= createRouter({
    history: createWebHistory(),
    routes
})
export default router;