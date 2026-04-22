import {createRouter,createWebHistory} from 'vue-router';
import Profile from '@/components/pages/users/Profile.vue';
import Dashboard from '@/components/pages/users/Dashboard.vue'
import Login from '@/components/pages/users/Login.vue'
import AdminDashboard from '@/components/pages/admin/AdminDashboard.vue';
import CreateGlobalEvent from '@/components/pages/admin/CreateGlobalEvent.vue';
import createEmergencyAnnouncement from '@/components/pages/admin/createEmergencyAnnouncement.vue';
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import EditProfile from '@/components/pages/forms/EditProfile.vue';
import Register from '@/components/pages/users/Register.vue';
import Events from '@/components/pages/users/Events.vue';
import Articles from '../components/pages/users/Articles.vue';
import Forum from '@/components/pages/users/Forum.vue';
import Feedback from '@/components/pages/forms/Feedback.vue';
import MarketPlace from '@/components/pages/users/MarketPlace.vue';
import Settings from '@/components/pages/users/Settings.vue';
import AdminUsers from '@/components/pages/admin/AdminUsers.vue';
import ProductForm from '@/components/pages/forms/ProductForm.vue';
import ArticleForm from '@/components/pages/forms/ArticleForm.vue';
import SellerApplicationForm from '@/components/pages/forms/SellerApplicationForm.vue';
import ClubsAndSocieties from '@/components/pages/users/ClubsAndSocieties.vue';
import GroupRegistrationForm from '@/components/pages/forms/GroupRegistrationForm.vue';
import ClubPublicProfile from '@/components/pages/clubs/ClubPublicProfile.vue';
import ManageClub from '@/components/pages/clubs/ManageClub.vue';
import Donate from '@/components/pages/users/Donate.vue';
import SellerProfile from '@/components/pages/users/SellerProfile.vue';

const routes=[{
    path:'/',
    component:DefaultLayout,
    children:[ 
        {path:'home',name:'Home',component:Dashboard},
        {path:'events',name:'Events',component:Events},
        {path:'articles',name:'Articles',component: Articles},
        {path:'forums',name:'Forums',component:Forum},
        {path:'marketplace',name:'Marketplace',component:MarketPlace},
        {path:'feedback',name:'Feedback',component:Feedback},
        {path:'profile',name:'Profile',component:Profile},
        {path:'settings',name:'Settings',component: Settings},
        {path:'editprofile',name:'EditProfile',component:EditProfile},
        {path:'addProduct',name:'AddProduct',component:ProductForm},
        {path: 'newArticle',name:'NewArticle',component:ArticleForm},
        {path:'applySeller', name:'ApplySeller',component:SellerApplicationForm},
        {path:'clubsAndSocieties',name:'ClubsAndSocieties',component:ClubsAndSocieties},
        {path:'registerClubOrSociety', name :'registerClubOrSociety',component:GroupRegistrationForm},
        {path: '/clubsAndSocieties/:id',name: 'ClubProfile',component: ClubPublicProfile },
        {path:'/clubs/:id/manage',name:'ManageClub',component:ManageClub},
        {path:'/donate',name:'Donations',component:Donate},
        { path: '/seller/:id', component: SellerProfile }
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
        children: [
        { path: '',component: AdminDashboard},
        { path: '/admin/events/create',name: 'AdminCreateEvent', component: CreateGlobalEvent},
        { path: '/admin/announcements/create', name: 'AdminCreateAnnouncement',component: createEmergencyAnnouncement}]
    }
]

const router= createRouter({
    history: createWebHistory(),
    routes
})
export default router;