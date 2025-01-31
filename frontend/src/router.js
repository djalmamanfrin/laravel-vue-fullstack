import {createRouter, createWebHistory} from "vue-router";
import DefaultLayout from "./components/DefaultLayout.vue";
import Home from "./pages/Home.vue";
import MyImages from "./pages/MyImages.vue";

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    children: [
      {path: '/', name: 'Home', component: Home},
      {path: '/my-images', name: 'MyImages', component: MyImages},
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
