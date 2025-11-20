<template>
  <div class="auth-container">
    <div class="auth-content">
      <router-view />
    </div>
  </div>
</template>

<style scoped>
.auth-container {
  height: 100vh;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: #f5f5f5; /* Or any background */
}

.auth-content {
  width: 400px;
  padding: 2rem;
  border-radius: 10px;
  background: white;
  box-shadow: 0px 2px 12px rgba(0,0,0,0.1);
}
</style>
