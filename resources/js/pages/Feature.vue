<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue'

const features = [
  {
    icon: 'fa-solid fa-robot',
    title: 'AI-Powered Sentiment Analysis',
    description:
      'Our system intelligently analyzes product reviews to understand customer opinions and emotional tones.',
  },
  {
    icon: 'fa-solid fa-layer-group',
    title: 'Aspect-Based Insights',
    description:
      'We extract detailed insights for each product feature to help identify what customers truly value.',
  },
  {
    icon: 'fa-solid fa-chart-line',
    title: 'Smart Recommendation Engine',
    description:
      'Generate accurate, personalized product suggestions using deep learning models trained on real feedback.',
  },
  {
    icon: 'fa-solid fa-bolt',
    title: 'Real-Time Processing',
    description:
      'Get instant recommendations and analytics as new reviews are added.',
  },
  {
    icon: 'fa-solid fa-database',
    title: 'Massive Review Dataset',
    description:
      'Leverage data from millions of verified reviews to ensure reliability and coverage across product categories.',
  },
  {
    icon: 'fa-solid fa-chart-pie',
    title: 'Interactive Analytics Dashboard',
    description:
      'Visualize customer trends and satisfaction levels through an easy-to-use interface.',
  },
]
</script>

<template>
  <PublicLayout
    hero-title="AI Recommender System Features"
    hero-subtitle="Discover the technology behind smarter, data-driven recommendations."
  >
    <!-- Feature Section -->
    <section class="bg-neutral-900 py-20 px-6 text-center text-white">
      <h2 class="text-4xl font-bold mb-12">Powerful Features</h2>

      <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 max-w-7xl mx-auto"
      >
        <div
          v-for="(feature, index) in features"
          :key="index"
          class="bg-neutral-800 hover:bg-neutral-700 transition-all duration-300 rounded-2xl p-8 shadow-md shadow-cyan-500/10 hover:shadow-cyan-500/30 group"
        >
          <div
            class="text-5xl text-cyan-400 mb-6 group-hover:scale-110 transition-transform duration-300"
          >
            <i :class="feature.icon"></i>
          </div>
          <h3 class="text-xl font-semibold mb-3 text-cyan-300">
            {{ feature.title }}
          </h3>
          <p class="text-gray-300 text-sm leading-relaxed">
            {{ feature.description }}
          </p>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section
      class="bg-gradient-to-r from-white/60 backdrop-blur-md to-white/50 text-white text-center py-20"
    >
      <h2 class="text-3xl font-bold mb-4">
        Experience the Power of AI Recommendations
      </h2>
      <p class="text-white/90 mb-6">
        Boost user engagement and satisfaction with real-time intelligent
        insights.
      </p>
      <a
        href="/register"
        class="bg-primary  text-white px-8 py-3 rounded-full font-semibold hover:bg-gray-200 transition"
      >
        Get Started
      </a>
    </section>

 <!-- How It Works Section -->
<section class="bg-neutral-950 py-24 px-6 text-center text-white">
  <h2 class="text-4xl font-bold mb-16">How It Works</h2>

  <div class="relative flex flex-wrap justify-center items-start gap-10 max-w-6xl mx-auto">
    <!-- Connector Line -->
    <div class="absolute top-1/2 left-0 w-full h-1 bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 opacity-30 hidden md:block"></div>

    <!-- Step 1 -->
    <div
      class="relative bg-neutral-800 rounded-2xl w-64 h-70 shadow-lg p-6 z-10 flex flex-col justify-between transition-transform hover:-translate-y-2 hover:shadow-cyan-500/40"
    >
      <div
        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-cyan-500 w-16 h-16 rounded-full flex items-center justify-center text-3xl shadow-lg"
      >
        <i class="fa-solid fa-comments"></i>
      </div>

      <div class="mt-10 flex-1 flex flex-col justify-center">
        <h3 class="text-xl font-semibold mb-2 text-cyan-400">
          Collect Reviews
        </h3>
        <p class="text-gray-300 text-sm leading-relaxed">
          Gather authentic customer feedback from multiple sources.
        </p>
      </div>

      <div class="text-3xl font-bold text-cyan-500/40">01</div>
    </div>

    <!-- Step 2 -->
    <div
      class="relative bg-neutral-800 rounded-2xl w-64 h-70 shadow-lg p-6 z-10 flex flex-col justify-between transition-transform hover:-translate-y-2 hover:shadow-blue-500/40"
    >
      <div
        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center text-3xl shadow-lg"
      >
        <i class="fa-solid fa-brain"></i>
      </div>

      <div class="mt-10 flex-1 flex flex-col justify-center">
        <h3 class="text-xl font-semibold mb-2 text-blue-400">
          Analyze Sentiments
        </h3>
        <p class="text-gray-300 text-sm leading-relaxed">
          NLP models detect sentiment polarity and emotion intensity.
        </p>
      </div>

      <div class="text-3xl font-bold text-blue-500/40">02</div>
    </div>

    <!-- Step 3 -->
    <div
      class="relative bg-neutral-800 rounded-2xl w-64 h-70 shadow-lg p-6 z-10 flex flex-col justify-between transition-transform hover:-translate-y-2 hover:shadow-indigo-500/40"
    >
      <div
        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-indigo-500 w-16 h-16 rounded-full flex items-center justify-center text-3xl shadow-lg"
      >
        <i class="fa-solid fa-filter"></i>
      </div>

      <div class="mt-10 flex-1 flex flex-col justify-center">
        <h3 class="text-xl font-semibold mb-2 text-indigo-400">
          Extract Aspects
        </h3>
        <p class="text-gray-300 text-sm leading-relaxed">
          Identify product aspects such as quality, price, and usability.
        </p>
      </div>

      <div class="text-3xl font-bold text-indigo-500/40">03</div>
    </div>

    <!-- Step 4 -->
    <div
      class="relative bg-neutral-800 rounded-2xl w-64 h-70 shadow-lg p-6 z-10 flex flex-col justify-between transition-transform hover:-translate-y-2 hover:shadow-purple-500/40"
    >
      <div
        class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-purple-600 w-16 h-16 rounded-full flex items-center justify-center text-3xl shadow-lg"
      >
        <i class="fa-solid fa-rocket"></i>
      </div>

      <div class="mt-10 flex-1 flex flex-col justify-center">
        <h3 class="text-xl font-semibold mb-2 text-purple-400">
          Recommend Products
        </h3>
        <p class="text-gray-300 text-sm leading-relaxed">
          AI engine suggests top-rated products personalized for each user.
        </p>
      </div>

      <div class="text-3xl font-bold text-purple-500/40">04</div>
    </div>
  </div>
</section>

  </PublicLayout>
</template>

<style scoped>
section {
  scroll-margin-top: 80px;
}
</style>
