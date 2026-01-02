declare module '@webzlodimir/vue-bottom-sheet' {
  import { DefineComponent, VNode } from 'vue';

  const VueBottomSheet: DefineComponent<
    {
      maxWidth?: number;
      maxHeight?: number;
    },
    {
      open: () => void;
      close: () => void;
    },
    {}, // data
    {}, // computed
    {}, // methods
    {}, // mixins
    {}, // extends
    {
      header?: () => VNode[]; // Define 'header' slot
    }
  >;

  export default VueBottomSheet;
}
