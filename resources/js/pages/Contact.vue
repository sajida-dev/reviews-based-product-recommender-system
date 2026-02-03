<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue'

const onSubmit = (event: Event) => {
  event.preventDefault()
}
</script>

<template>
  <PublicLayout 
    hero-title="Contact Us"
    hero-subtitle="Empowering smarter decisions through intelligent review analysis."
    >
    <!-- TOP FORM SECTION -->
    <section class="bg-neutral-800 py-20 px-6">
      <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-4xl font-extrabold text-neutral-200 mb-3">
          Contact Us
        </h1>
        <p class="text-neutral-300 mb-10">
          Any questions or remarks? Just write us a message!
        </p>

        <!-- Inputs -->
        <form @submit="onSubmit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="text-left">
             
              <input
                type="email"
                placeholder="Enter a valid email address"
                class="w-full bg-neutral-300 px-6 py-3 rounded-full
                       placeholder-neutral-600 focus:outline-none"
                required
              />
            </div>

            <div class="text-left">
              
              <input
                type="text"
                placeholder="Enter your Name"
                class="w-full bg-neutral-300 px-6 py-3 rounded-full
                       placeholder-neutral-600 focus:outline-none"
                required
              />
            </div>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            class="w-full bg-primary text-white py-3 rounded-full 
                   font-semibold tracking-widest text-sm"
          >
            SUBMIT
          </button>
        </form>
      </div>
    </section>

    <!-- INFO STRIP -->
    <section class="bg-neutral-600 py-16 px-6">
      <div
        class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12  text-center"
      >
        <!-- ABOUT -->
        <div >
          <div
            class="w-25 h-25 mx-auto mb-6 flex items-center justify-center
                   rounded-full bg-primary text-white text-2xl"
          >
            <i class="fa-solid fa-person-running"></i>
          </div>
          <h3 class="font-bold text-white tracking-wide mb-2">
            ABOUT CLUB
          </h3>
          <p class="text-sm text-neutral-300">
            Running Guide<br />
            Workouts
          </p>
        </div>

        <!-- PHONE -->
        <div>
          <div
            class="w-25 h-25 mx-auto mb-6 flex items-center justify-center
                   rounded-full bg-primary text-white text-2xl"
          >
            <i class="fa-solid fa-phone"></i>
          </div>
          <h3 class="font-bold text-white tracking-wide mb-2">
            PHONE (LANDLINE)
          </h3>
          <p class="text-sm text-neutral-300">
            +92 300 1234567<br />
            +92 42 12345678
          </p>
        </div>

        <!-- LOCATION -->
        <div>
          <div
            class="w-25 h-25 mx-auto mb-6 flex items-center justify-center
                   rounded-full bg-primary text-white text-2xl"
          >
            <i class="fa-solid fa-location-dot"></i>
          </div>
          <h3 class="font-bold text-white tracking-wide mb-2">
            OUR OFFICE LOCATION
          </h3>
          <p class="text-sm text-neutral-300">
            Lahore, Pakistan<br />
            AI Innovation Center
          </p>
        </div>
      </div>
    </section>
  </PublicLayout>
</template>
