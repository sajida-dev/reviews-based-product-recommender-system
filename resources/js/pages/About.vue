<script setup lang="ts">
import TeamSection from '@/components/TeamSection.vue';
import PublicLayout from '@/layouts/PublicLayout.vue'

const teamMembers = [
  {
    name: 'Areeba Parveen',
    role: 'Lead AI Engineer',
    icon: 'fa-solid fa-brain',
  },
  {
    name: 'Ali Javed',
    role: 'Frontend Developer',
    icon: 'fa-solid fa-code',
  },
  {
    name: 'Sara Khan',
    role: 'UI/UX Designer',
    icon: 'fa-solid fa-palette',
  },
]
</script>

<template>
  <PublicLayout
    hero-title="About Our AI Recommender"
    hero-subtitle="Empowering smarter decisions through intelligent review analysis."
  >
    <!-- About Section -->
    <section class="bg-neutral-950 py-20 px-6 text-center text-white">
      <h2 class="text-4xl font-bold mb-6">Who We Are</h2>
      <p class="text-gray-300 max-w-3xl mx-auto leading-relaxed">
        We are a team of passionate developers, data scientists, and designers
        building next-generation AI tools for understanding customer feedback.
        Our goal is to simplify decision-making by transforming raw reviews into
        actionable insights using advanced NLP and machine learning models.
      </p>
    </section>

    <!-- Mission Section -->
    <section class="bg-neutral-900 py-20 px-6 text-center text-white">
      <h2 class="text-4xl font-bold mb-10">Our Mission</h2>
      <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 text-left">
        <div>
          <h3 class="text-cyan-400 text-2xl font-semibold mb-4">
            AI for Better Product Choices
          </h3>
          <p class="text-gray-300 leading-relaxed">
            We help users find the best products based on real customer
            experiences. Our models analyze millions of reviews to uncover
            trends, preferences, and satisfaction levels.
          </p>
        </div>
        <div>
          <h3 class="text-cyan-400 text-2xl font-semibold mb-4">
            Transparency & Trust
          </h3>
          <p class="text-gray-300 leading-relaxed">
            We value authentic opinions. Our recommendation engine ensures that
            users see unbiased, data-driven insights—not sponsored promotions.
          </p>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <TeamSection />
  </PublicLayout>
</template>
