<script setup lang="ts">
import type { SidebarProps } from '.'
import { cn } from '@/lib/utils'
import { Sheet, SheetContent } from '@/components/ui/sheet'
import SheetDescription from '@/components/ui/sheet/SheetDescription.vue'
import SheetHeader from '@/components/ui/sheet/SheetHeader.vue'
import SheetTitle from '@/components/ui/sheet/SheetTitle.vue'
import { SIDEBAR_WIDTH_MOBILE, useSidebar } from './utils'

defineOptions({
  inheritAttrs: false,
})

const props = withDefaults(defineProps<SidebarProps>(), {
  side: 'left',
  variant: 'sidebar',
  collapsible: 'offcanvas',
})

const { isMobile, state, openMobile, setOpenMobile } = useSidebar()
</script>

<template>
  <!-- Sidebar for no-collapsible -->
  <div v-if="collapsible === 'none'" data-slot="sidebar" :class="cn(
    'bg-white/10 backdrop-blur-lg text-white flex h-full w-(--sidebar-width) flex-col shadow-lg rounded-r-xl border-r border-white/20',
    props.class
  )" v-bind="$attrs">
    <slot />
  </div>

  <!-- Mobile Sidebar -->
  <Sheet v-else-if="isMobile" :open="openMobile" v-bind="$attrs" @update:open="setOpenMobile">
    <SheetContent data-sidebar="sidebar" data-slot="sidebar" data-mobile="true" :side="side"
      class="bg-white/10 backdrop-blur-lg text-white w-(--sidebar-width) p-0 [&>button]:hidden rounded-r-xl shadow-lg border border-white/20"
      :style="{ '--sidebar-width': SIDEBAR_WIDTH_MOBILE }">
      <SheetHeader class="sr-only">
        <SheetTitle>Sidebar</SheetTitle>
        <SheetDescription>Displays the mobile sidebar.</SheetDescription>
      </SheetHeader>
      <div class="flex h-full w-full flex-col">
        <slot />
      </div>
    </SheetContent>
  </Sheet>

  <!-- Desktop Sidebar -->
  <div v-else class="group peer text-white hidden md:block" data-slot="sidebar" :data-state="state"
    :data-collapsible="state === 'collapsed' ? collapsible : ''" :data-variant="variant" :data-side="side">
    <div :class="cn(
      'relative w-(--sidebar-width) bg-transparent transition-[width] duration-200 ease-linear',
      'group-data-[collapsible=offcanvas]:w-0'
    )" />
    <div :class="cn(
      'fixed inset-y-0 z-10 hidden h-svh w-(--sidebar-width) transition-[left,right,width] duration-200 ease-linear md:flex',
      side === 'left' ? 'left-0 group-data-[collapsible=offcanvas]:left-[calc(var(--sidebar-width)*-1)]' : 'right-0 group-data-[collapsible=offcanvas]:right-[calc(var(--sidebar-width)*-1)]',
      variant === 'floating' || variant === 'inset' ? 'p-2 group-data-[collapsible=icon]:w-[calc(var(--sidebar-width-icon)+(--spacing(4))+2px)]' : 'group-data-[collapsible=icon]:w-(--sidebar-width-icon) group-data-[side=left]:border-r group-data-[side=right]:border-l',
      props.class
    )" v-bind="$attrs">
      <div data-sidebar="sidebar"
        class="bg-white/10 backdrop-blur-lg border border-white/20 flex h-full w-full flex-col rounded-r-xl shadow-lg">
        <slot />
      </div>
    </div>
  </div>
</template>
