<template>
  <v-app>
    <v-main class="bg-background-light position-relative">
      
      <div class="visual-accent-top"></div>
      <div class="visual-accent-bottom"></div>

      <v-container fluid class="fill-height d-flex align-center justify-center" style="z-index: 1;">
        
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>

      </v-container>

      <v-footer app class="bg-transparent justify-center text-caption text-grey position-absolute w-100" style="bottom: 0;">
        © 2026 The Co-operative University of Kenya
      </v-footer>

    </v-main>
  </v-app>
</template>

<style scoped>
/* Define the blobs here so they appear on ALL auth pages */
.visual-accent-top {
  position: fixed;
  top: -128px;
  right: -128px;
  width: 256px;
  height: 256px;
  background-color: rgba(236, 127, 19, 0.05);
  border-radius: 50%;
  filter: blur(64px);
}

.visual-accent-bottom {
  position: fixed;
  bottom: -192px;
  left: -192px;
  width: 384px;
  height: 384px;
  background-color: rgba(236, 127, 19, 0.1);
  border-radius: 50%;
  filter: blur(64px);
}
</style>