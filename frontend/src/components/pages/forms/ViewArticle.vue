<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import * as articleService from '@/services/articleService';
import {postComment} from '@/services/articleService';

const route = useRoute();
const articleId = route.params.id;

// State
const article = ref<any>(null);
const comments = ref<any[]>([]);
const newComment = ref('');
const isSubmitting = ref(false);

// Split content by newlines for better paragraph rendering
const articleParagraphs = computed(() => {
  return article.value?.content ? article.value.content.split('\n') : [];
});

const fetchData = async () => {
  try {
    // 1. Fetch Article
    const artRes = await articleService.getArticle(articleId as string);
    article.value = artRes.data?.data || artRes.data || artRes;

    // 2. Fetch Comments
    const comRes = await articleService.getComments(articleId as string);
    comments.value = comRes.data?.data || comRes.data || [];
  } catch (error) {
    console.error("Error loading page:", error);
  }
};

const submitComment = async () => {
  if (!newComment.value.trim()) return;
  isSubmitting.value = true;
  try {
    const res = await articleService.postComment(articleId as string, { content: newComment.value });
    comments.value.unshift(res.data?.data || res.data);
    newComment.value = '';
  } catch (error) {
    console.error("Error posting comment:", error);
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(fetchData);
</script>
<template>
  <div class="bg-background min-h-screen pb-12">
    <v-img
      v-if="article"
      :src="article.image"
      height="450"
      cover
      class="align-end"
    >
      <div class="fill-height" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
        <v-container class="pb-10">
          <v-btn
            prepend-icon="mdi-arrow-left"
            variant="text"
            color="white"
            class="text-none mb-6"
            @click="$router.back()"
          >
            Back to Stories
          </v-btn>
          
          <v-chip color="primary" variant="flat" class="mb-4 font-weight-bold text-uppercase">
            {{ article.category }}
          </v-chip>
          <h1 class="text-h2 font-weight-black text-white mb-4 leading-tight" style="max-width: 900px;">
            {{ article.title }}
          </h1>
        </v-container>
      </div>
    </v-img>

    <v-container style="max-width: 900px; margin-top: -60px;">
      <v-card class="rounded-xl elevation-3 border-opacity-50 pa-6 pa-md-12 bg-surface" border>
        
        <div class="d-flex flex-wrap align-center justify-space-between mb-10 pb-6 border-b">
          <div class="d-flex align-center gap-4">
            <v-avatar size="56" class="elevation-2 border">
              <v-img :src="article.user?.avatar || article.authorAvatar"></v-img>
            </v-avatar>
            <div>
              <div class="text-h6 font-weight-bold">{{ article.user?.fullname || article.authorName }}</div>
              <div class="text-body-2 text-medium-emphasis">
                {{ article.date }} • {{ article.readTime || '5 min read' }}
              </div>
            </div>
          </div>
          
          <div class="d-flex gap-2">
            <v-btn icon="mdi-share-variant-outline" variant="tonal" size="small"></v-btn>
            <v-btn icon="mdi-bookmark-outline" variant="tonal" size="small"></v-btn>
          </div>
        </div>

        <div class="article-content text-body-1 text-high-emphasis">
          <p v-for="(paragraph, index) in articleParagraphs" :key="index" class="mb-6">
            {{ paragraph }}
          </p>
        </div>

        <v-divider class="my-10"></v-divider>

        <section>
          <div class="d-flex align-center justify-space-between mb-8">
            <h3 class="text-h5 font-weight-black">Discussion ({{ comments.length }})</h3>
          </div>

          <div class="d-flex gap-4 mb-10">
            <v-avatar size="48" color="primary" variant="tonal">
              <v-icon icon="mdi-account"></v-icon>
            </v-avatar>
            <div class="flex-grow-1">
              <v-textarea
                v-model="newComment"
                placeholder="What are your thoughts?"
                variant="outlined"
                auto-grow
                rows="2"
                hide-details
                class="rounded-lg mb-3"
              ></v-textarea>
              <v-btn 
                color="primary" 
                variant="flat" 
                class="text-none font-weight-bold px-8 rounded-lg"
                :loading="isSubmitting"
                :disabled="!newComment.trim()"
                @click="submitComment"
              >
                Post Comment
              </v-btn>
            </div>
          </div>

          <div v-for="comment in comments" :key="comment.id" class="d-flex gap-4 mb-8">
            <v-avatar size="44" class="border">
              <v-img :src="comment.user?.avatar"></v-img>
            </v-avatar>
            <div class="bg-grey-lighten-4 pa-5 rounded-xl rounded-ts-0 flex-grow-1">
              <div class="d-flex justify-space-between align-center mb-1">
                <span class="font-weight-bold">{{ comment.user?.fullname }}</span>
                <span class="text-caption text-medium-emphasis">
                  {{ new Date(comment.created_at).toLocaleDateString() }}
                </span>
              </div>
              <p class="text-body-2 mb-0">{{ comment.content }}</p>
            </div>
          </div>
        </section>
      </v-card>
    </v-container>
  </div>
</template>
<style scoped>
.article-content {
  line-height: 1.8;
  font-size: 1.15rem;
  letter-spacing: 0.01em;
}
.gap-4 { gap: 16px; }
</style>