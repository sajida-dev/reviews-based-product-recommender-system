// resources/js/shims-vue.d.ts

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

declare module 'vue3-easy-data-table' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component

  export interface Header {
    text: string
    value: string
    sortable?: boolean
    width?: string | number
  }

  export interface Item {
    [key: string]: any
  }
}
