import {createRouter,createWebHistory} from 'vue-router';
import Profile from '@/components/pages/users/Profile.vue';
import Dashboard from '@/components/pages/users/Dashboard.vue'
import Login from '@/components/pages/users/Login.vue'
import AdminDashboard from '@/components/pages/admin/AdminDashboard.vue';
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import EditProfile from '@/components/pages/forms/EditProfile.vue';
import Register from '@/components/pages/users/Register.vue';
const routes=[{
    path:'/',
    component:DefaultLayout,
    children:[ {path:'/',name:'Home',component:Dashboard},
        {path:'/events',name:'Events',component:Event},
        // {path:'/articles',name:'Articles'}, typescript enforces need for component. ensure you put it.
        // {path:'/forums',name:'Forums'},
        // {path:'/marketplace',name:'Marketplace'},
        // {path:'/feedback',name:'Feedback'},
        {path:'/profile',name:'Profile',component:Profile},
        // {path:'/settings',name:'Settings'},
        {path:'/editprofile',name:'EditProfile',component:EditProfile}
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
        path:'/register',
        component:AuthLayout,
        children:[
            { path:'/register',name:'register',component:Register}
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