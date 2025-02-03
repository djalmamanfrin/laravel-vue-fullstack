import {createRouter, createWebHistory} from "vue-router";
import DefaultLayout from "./components/DefaultLayout.vue";
import Signup from "./pages/Signup.vue";
import Login from "./pages/Login.vue";
import Home from "./pages/Home.vue";
import NotFound from "./pages/NotFound.vue";
import BoardView from "./pages/BoardView.vue";
import useUserStore from "./store/user.js";

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    children: [
      {path: '/', name: 'Home', component: Home},
      {path: '/my-images', name: 'MyImages', component: MyImages},
      {path: '/board/:id', name: 'Board', component: BoardView},
    ],
    beforeEnter: async (to, from, next) => {
      try {
        const userStore = useUserStore();
        await userStore.fetchUser();
        next();
      } catch (error) {
        next(false);
      }
    },
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/signup',
    name: 'Signup',
    component: Signup,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
