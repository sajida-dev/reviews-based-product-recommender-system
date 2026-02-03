<script setup lang="ts">
const teamMembers = [
  {
    name: 'Sajida Javed',
    role: 'AI Engineer',
    image:
      'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8R8QqmvD2GHfMgcUODxvVme8Cm6unpEDQ8w&s',
    socials: {
      linkedin: 'https://linkedin.com/in/sajidajaved',
      github: 'https://github.com/sajida-dev',
      kaggle: 'https://www.kaggle.com/sajida-dev',
      huggingface: 'https://huggingface.co/sajidajaved',
      gmail: 'mailto:sajida.javed@example.com',
    },
  },
  {
    name: 'Areeba Parveen',
    role: 'Web Engineer',
    image:
      'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ8R8QqmvD2GHfMgcUODxvVme8Cm6unpEDQ8w&s',
    socials: {
      linkedin: 'https://linkedin.com/in/areebaparveen',
      github: 'https://github.com/areebaparveen',
      kaggle: 'https://www.kaggle.com/areebaparveen',
      huggingface: 'https://huggingface.co/areebaparveen',
      gmail: 'mailto:areeba.parveen@example.com',
    },
  },
]
</script>

<template>
  <section class="bg-neutral-950 py-20 px-6 text-center text-white">
    <h2 class="text-4xl font-bold mb-12">Meet Our Team</h2>

    <div class="flex flex-wrap justify-center gap-10 max-w-5xl mx-auto">
      <div
        v-for="(member, index) in teamMembers"
        :key="index"
        class="bg-white/70 text-gray-900 rounded-2xl shadow-lg w-72 p-6 flex flex-col items-center hover:shadow-primary/40 transition-transform hover:-translate-y-2"
      >
        <!-- Profile Image -->
        <div
          class="w-32 h-32 rounded-full border-8 border-primary overflow-hidden mb-4"
        >
          <img
            :src="member.image"
            :alt="member.name"
            class="w-full h-full object-cover"
          />
        </div>

        <!-- Name & Role -->
        <h3 class="text-xl font-bold text-black">{{ member.name }}</h3>
        <p class="text-primary font-medium mt-1">{{ member.role }}</p>

        <!-- Social Icons -->
        <div class="flex justify-center gap-4 mt-5 text-primary text-lg">
          <!-- LinkedIn -->
          <a
            :href="member.socials.linkedin"
            target="_blank"
            class="hover:scale-110 transition"
            title="LinkedIn"
          >
            <i class="fa-brands fa-linkedin-in"></i>
          </a>

          <!-- GitHub -->
          <a
            :href="member.socials.github"
            target="_blank"
            class="hover:scale-110 transition"
            title="GitHub"
          >
            <i class="fa-brands fa-github"></i>
          </a>

          <!-- Kaggle -->
          <a
            :href="member.socials.kaggle"
            target="_blank"
            class="hover:scale-110 transition"
            title="Kaggle"
          >
            <i class="fa-brands fa-kaggle"></i>
          </a>

          <!-- Hugging Face -->
          <a
            :href="member.socials.huggingface"
            target="_blank"
            class="hover:scale-110 transition"
            title="Hugging Face"
          >
            <i class="fa-solid fa-face-smile"></i>
          </a>

          <!-- Gmail -->
          <a
            :href="member.socials.gmail"
            class="hover:scale-110 transition"
            title="Gmail"
          >
            <i class="fa-solid fa-envelope"></i>
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
section {
  scroll-margin-top: 80px;
}

/* Slight glowing hover for icons */
a:hover i {
  color: #22d3ee; /* cyan-400 */
  text-shadow: 0 0 8px rgba(34, 211, 238, 0.6);
}
</style>
