<template>
    <div>
        <div class="flex flex-wrap gap-2 mb-2 justify-end">
            <!-- Import/Export buttons as before -->
            <slot name="toolbar" />
        </div>
        <EasyDataTable :headers="headers" :items="items" :server-items-length="serverItemsLength"
            :server-options="serverOptions" :loading="loading" :expandable="expandable" :expand-row-keys="expandRowKeys"
            server-mode table-class-name="customize-table" header-text-direction="center" body-text-direction="center"
            @update:server-options="$emit('update:server-options', $event)">
            <template v-for="(_, slot) in $slots" #[slot]="slotProps">
                <slot :name="slot" v-bind="slotProps" />
            </template>
        </EasyDataTable>
    </div>
</template>

<script setup lang="ts">
import EasyDataTable from 'vue3-easy-data-table';
import { computed } from 'vue';

const props = defineProps({
    headers: { type: Array, required: true },
    items: { type: Array, required: true },
    loading: { type: Boolean, default: false },
    serverOptions: { type: Object, default: undefined },
    serverItemsLength: { type: Number, default: 0 },
    theme: { type: String, default: 'dark' },
    expandable: { type: Boolean, default: false },
    expandRowKeys: { type: Array, default: () => [] },
});


defineSlots<Record<string, (props: any) => any>>()

// Use system-wide theme colors (purple, blue, gray, etc.)
const tableClass = computed(() =>
    'min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900 rounded-lg shadow overflow-x-auto'
);
const headerClass = computed(() =>
    'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100'
);
const rowClass = computed(() =>
    'hover:bg-purple-50 dark:hover:bg-purple-900 transition cursor-pointer'
);
</script>

<style>
.customize-table {
    /* Light theme (default) - do not change */
    --easy-table-border: 1px solid #eeeff1;
    /* Tailwind gray-200 */
    --easy-table-row-border: 1px solid #e5e7eb;

    --easy-table-header-font-color: #18181b;
    /* Tailwind gray-900 */
    --easy-table-header-background-color: #f3f4f6;
    --easy-table-body-even-row-font-color: #18181b;
    --easy-table-body-even-row-background-color: #fff;

    --easy-table-body-row-font-color: #18181b;
    --easy-table-body-row-background-color: #f9fafb;

    --easy-table-body-row-hover-font-color: #181818;
    --easy-table-body-row-hover-background-color: #e6e6e7;

    --easy-table-footer-background-color: #f3f4f6;
    --easy-table-footer-font-color: #18181b;

    --easy-table-scrollbar-track-color: #f3f4f6;
    --easy-table-scrollbar-color: #f3f4f6;
    --easy-table-scrollbar-thumb-color: #b7b7b7;
    --easy-table-scrollbar-corner-color: #f3f4f6;

    --easy-table-loading-mask-background-color: #f3f4f6;
}

.dark .customize-table {
    /* Dark theme - use only Tailwind theme colors already used in your project */
    --easy-table-border: 1px solid #27272a;
    /* Tailwind gray-800 */
    --easy-table-row-border: 1px solid #27272a;

    --easy-table-header-font-color: #f3f4f6;
    /* Tailwind gray-100 */
    --easy-table-header-background-color: #18181b;
    /* Tailwind gray-900 */

    --easy-table-body-even-row-font-color: #f3f4f6;
    /* Tailwind gray-100 */
    --easy-table-body-even-row-background-color: #23232a;
    /* slightly lighter than gray-900 */

    --easy-table-body-row-font-color: #e5e7eb;
    /* Tailwind gray-200 */
    --easy-table-body-row-background-color: #18181b;
    /* Tailwind gray-900 */

    --easy-table-body-row-hover-font-color: #dfdfe2;
    /* Tailwind gray-900 */
    --easy-table-body-row-hover-background-color: #363637;
    /* Tailwind purple-400 */

    --easy-table-footer-background-color: #18181b;
    /* Tailwind gray-900 */
    --easy-table-footer-font-color: #e5e7eb;
    /* Tailwind gray-200 */

    --easy-table-scrollbar-track-color: #18181b;
    --easy-table-scrollbar-color: #18181b;
    --easy-table-scrollbar-thumb-color: #b7b7b7;
    --easy-table-scrollbar-corner-color: #18181b;

    --easy-table-loading-mask-background-color: #18181b;
    /* For empty state background, use a slightly lighter color for contrast */
    --easy-table-empty-font-color: #b7b7b7;
}
</style>